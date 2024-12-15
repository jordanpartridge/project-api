<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Models\Project;
use Filament\Forms\Components\Actions\Action as TableAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Projects';

    protected static ?string $navigationGroup = 'Development';

    protected static ?string $modelLabel = 'Project';

    protected static ?string $pluralModelLabel = 'Projects';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Project Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter project name')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->required()
                            ->maxLength(65535)
                            ->placeholder('Enter project description')
                            ->columnSpanFull(),
                    ]),

                Fieldset::make('Github Repo')
                    ->relationship('repo')
                    ->schema([
                        TextInput::make('full_name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('organization/repository')
                            ->columnSpanFull()
                            ->suffixAction(
                                TableAction::make('viewRepo')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->color('gray')
                                    ->url(fn ($record) => '/admin/repos/'.$record?->id)
                                    ->visible(fn ($record) => $record?->id)
                                    ->openUrlInNewTab()
                            ),
                        TextInput::make('url')
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('description')
                            ->columnSpanFull(),
                    ])
                    ->extraAttributes(['class' => 'relative']),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->wrap(),
                ViewColumn::make('repo.full_name')
                    ->view('tables.columns.github-repo-badge')
                    ->state(fn ($record) => [
                        'name' => $record->repo->full_name,
                        'url' => route('filament.admin.resources.repos.view', $record->repo),
                    ])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->limit(50)
                    ->tooltip(fn (Project $record): string => $record->description ?? ''),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => $state->format('M j, Y g:i A'))
                    ->sortable()
                    ->toggleable()
                    ->tooltip(fn (Project $record): string => $record->created_at->format('F j, Y g:i:s A')),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => $state->diffForHumans())
                    ->sortable()
                    ->toggleable()
                    ->tooltip(fn (Project $record): string => $record->updated_at->format('F j, Y g:i:s A')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('stars')
                    ->options([
                        '10' => '10+ stars',
                        '50' => '50+ stars',
                        '100' => '100+ stars',
                        '1000' => '1000+ stars',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $stars): Builder => $query->whereHas('repo', fn ($q) => $q->where('stars_count', '>=', (int) $stars)
                            )
                        );
                    }),

                Filter::make('updated_recently')
                    ->query(fn (Builder $query): Builder => $query->where('updated_at', '>=', now()->subDays(7)))
                    ->label('Updated This Week'),

            ])
            ->actions([
                ViewAction::make()
                    ->color('gray'),
                Action::make('viewOnGithub')
                    ->label('Open in GitHub')
                    ->icon('heroicon-m-code-bracket')
                    ->color('gray')
                    ->button()
                    ->size('sm')
                    ->outlined()
                    ->extraAttributes([
                        'class' => 'flex items-center gap-1 border-gray-700 bg-gray-800 text-gray-200 hover:bg-gray-700',
                    ])
                    ->url(fn (Project $record) => $record->repo?->url)
                    ->openUrlInNewTab()
                    ->visible(fn (Project $record) => $record->repo?->url),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
