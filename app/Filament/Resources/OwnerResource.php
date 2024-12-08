<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OwnerResource\Pages\CreateOwner;
use App\Filament\Resources\OwnerResource\Pages\EditOwner;
use App\Filament\Resources\OwnerResource\Pages\ListOwners;
use App\Filament\Resources\OwnerResource\Pages\ViewOwner;
use App\Models\Owner;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Repository Owners';

    protected static ?string $navigationGroup = 'GitHub';

    protected static ?string $recordTitleAttribute = 'login';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Owner Information')
                    ->description('Manage repository owner details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('login')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Username')
                                    ->autocomplete(false)
                                    ->columnSpan(1),

                                TextInput::make('html_url')
                                    ->label('GitHub URL')
                                    ->required()
                                    ->url()
                                    ->columnSpan(1)
                                    ->suffixIcon('heroicon-m-link'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('login')
                    ->label('Username')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('html_url')
                    ->label('GitHub Profile')
                    ->searchable()
                    ->copyable()
                    ->limit(30),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('login', 'asc')
            ->filters([
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->iconButton(),
                    EditAction::make()
                        ->iconButton(),
                    Action::make('view_github')
                        ->label('Open in GitHub')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->url(fn (Owner $record): string => $record->html_url)
                        ->openUrlInNewTab()
                        ->color('gray'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ])->iconButton(),
            ])
            ->emptyStateHeading('No Repository Owners')
            ->emptyStateDescription('Repository owners will appear here once created.')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOwners::route('/'),
            'create' => CreateOwner::route('/create'),
            'edit' => EditOwner::route('/{record}/edit'),
            'view' => ViewOwner::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
