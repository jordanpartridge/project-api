<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasShieldPermissions
{
    use HasApiTokens;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // Super admin can access everything
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return match ($panel->getId()) {
            'admin' => $this->can('view_admin_panel'),
            'github' => $this->can('view_github_panel'),
            default => false,
        };
    }

    public function getFilamentShieldPermissions(): array
    {
        return $this->roles->pluck('name')->toArray();
    }
}
