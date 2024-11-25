<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommitResource\Pages\CreateCommit;
use App\Filament\Resources\CommitResource\Pages\EditCommit;
use App\Filament\Resources\CommitResource\Pages\ListCommits;
use App\Models\Commit;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class CommitResource extends Resource
{
    protected static ?string $model = Commit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommits::route('/'),
            'create' => CreateCommit::route('/create'),
            'edit' => EditCommit::route('/{record}/edit'),
        ];
    }
}
