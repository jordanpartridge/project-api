<?php

namespace App\Filament\Resources\RepoResource\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CommitsRelationshipManager extends RelationManager
{
    protected static string $relationship = 'commits';

    protected static ?string $recordTitleAttribute = 'message';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('sha')
                    ->required()
                    ->maxLength(40),

                Textarea::make('message')
                    ->required()
                    ->maxLength(65535),

                TextInput::make('author')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                TextColumn::make('sha')
                    ->label('SHA')
                    ->formatStateUsing(fn (string $state) => Str::limit($state, 7))
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('SHA copied')
                    ->copyMessageDuration(1500)
                    ->toggleable(),

                TextColumn::make('message')
                    ->wrap()
                    ->limit(50)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    }),

                TextColumn::make('author')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
