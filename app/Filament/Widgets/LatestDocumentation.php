<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\DocumentationResource;
use App\Models\Documentation;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestDocumentation extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Documentation::latest()->limit(5))
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->description(fn (Documentation $record): string => str()->limit(strip_tags($record->content), 100)),

                TextColumn::make('category')
                    ->badge(),

                IconColumn::make('is_published')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('edit')
                    ->url(fn (Documentation $record): string => DocumentationResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
