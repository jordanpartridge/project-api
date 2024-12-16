<?php

namespace App\Filament\Resources\PrivateGPT;

use App\Filament\Resources\PrivateGPT\Pages;
use App\Filament\Resources\PrivateGPT\RelationManagers;
use App\Models\PrivateGPTAnalysis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrivateGPTResource extends Resource
{
    protected static ?string $model = PrivateGPTAnalysis::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('prompt')
                    ->required(),
                Forms\Components\Textarea::make('response')
                    ->nullable(),
                Forms\Components\KeyValue::make('metadata')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('prompt')->limit(50),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrivateGPTAnalyses::route('/'),
            'create' => Pages\CreatePrivateGPTAnalysis::route('/create'),
            'edit' => Pages\EditPrivateGPTAnalysis::route('/{record}/edit'),
        ];
    }
}