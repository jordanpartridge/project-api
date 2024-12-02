<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages\CreateFile;
use App\Filament\Resources\FileResource\Pages\EditFile;
use App\Filament\Resources\FileResource\Pages\ListFiles;
use App\Filament\Resources\RepoResource\RelationManagers\CommitsRelationshipManager;
use App\Models\File;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('filename'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ViewColumn::make('repo.full_name')
                    ->view('tables.columns.github-repo-badge')
                    ->state(fn ($record) => [
                        'name' => $record->repo->full_name,
                        'url' => route('filament.admin.resources.repos.view', $record->repo),
                    ])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('filename')
                    ->label('Filename')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content')
                    ->label('Content')
                    ->url(fn ($record) => $record->content)
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                // Add a direct visit action
                Action::make('visit')
                    ->label('Visit URL')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => $record->content)
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CommitsRelationshipManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFiles::route('/'),
            'create' => CreateFile::route('/create'),
            'edit' => EditFile::route('/{record}/edit'),
        ];
    }
}
