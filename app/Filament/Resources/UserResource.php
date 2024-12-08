<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'System User';
    protected static ?string $pluralModelLabel = 'System Users';
    protected static int $globalSearchResultsLimit = 20;

    public static function getNavigationBadge(): ?string
    {
        return Cache::remember('users_count', now()->addMinutes(10), function () {
            return static::getModel()::count();
        });
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = static::getModel()::count();

        return $count > 10 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Details')
                    ->description('Manage user personal information')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Full Name')
                                    ->autocomplete('name'),

                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('Email Address')
                                    ->autocomplete('email'),

                                TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->same('password_confirmation')
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->visible(fn (string $context): bool => $context === 'create')
                                    ->autocomplete('new-password'),

                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->visible(fn (string $context): bool => $context === 'create')
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->autocomplete('new-password'),

                                Select::make('roles')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->label('Roles')
                                    ->required()
                                    ->placeholder('Assign Roles'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable()
                    ->label('ID'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->icon('heroicon-o-user')
                    ->weight('medium'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                IconColumn::make('email_verified_at')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->label('Verified')
                    ->sortable()
                    ->toggleable()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('roles_display')
                    ->label('Roles')
                    ->getStateUsing(function ($record) {
                        $icons = [
                            'admin' => '🛡️',
                            'github_user' => '🐙',
                            'super_admin' => '👑',
                        ];

                        return $record->roles?->pluck('name')->map(fn ($role) => ($icons[$role] ?? '🔹') . ' ' . Str::title(str_replace('_', ' ', $role))
                        )->join(', ') ?? 'No roles assigned';
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->date('M j, Y')
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->since()
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->nullable()
                    ->trueLabel('Verified')
                    ->falseLabel('Not Verified')
                    ->placeholder('All Users'),

                SelectFilter::make('roles.name')
                    ->label('Role')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->placeholder('All Roles'),

            ])
            ->actions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->iconButton(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
