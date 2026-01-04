<?php

namespace BikeService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bike extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'model',
        'year',
        'serial_number',
        'description',
        'specifications',
        'status',
        'last_maintenance_at'
    ];

    protected $casts = [
        'specifications' => 'array',
        'last_maintenance_at' => 'datetime'
    ];

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}