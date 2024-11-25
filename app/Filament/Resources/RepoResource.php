<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepoResource\Pages\CreateRepo;
use App\Filament\Resources\RepoResource\Pages\EditRepo;
use App\Filament\Resources\RepoResource\Pages\ListRepos;
use App\Filament\Resources\RepoResource\Pages\ViewRepo;
use App\Models\Repo;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RepoResource extends Resource
{
    protected static ?string $model = Repo::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?string $navigationGroup = 'GitHub';

    protected static ?string $navigationLabel = 'Repositories';

    protected static ?string $modelLabel = 'Repository';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Repository Details')
                    ->schema([
                        TextInput::make('github_id')
                            ->required()
                            ->numeric(),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('full_name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Toggle::make('private')
                            ->default(false),

                        Select::make('project_id')
                            ->relationship('project', 'name')
                            ->required(),

                        Select::make('language_id')
                            ->relationship('language', 'name')
                            ->nullable(),

                        TextInput::make('stars_count')
                            ->numeric()
                            ->readonly()
                            ->default(0),

                        TextInput::make('forks_count')
                            ->numeric()
                            ->default(0),

                        TextInput::make('open_issues_count')
                            ->numeric()
                            ->default(0),

                        TextInput::make('default_branch')
                            ->default('main')
                            ->maxLength(255),

                        TextInput::make('license')
                            ->maxLength(255)
                            ->nullable(),

                        DateTimePicker::make('last_push_at')
                            ->nullable(),

                        // For JSON field, you might want to use a more specialized component
                        TextInput::make('topics')
                            ->helperText('Enter topics as comma-separated values')
                            ->nullable(),
                    ])
                    ->columns(2),

                View::make('filament.forms.components.github-stats')
                    ->visible(fn ($record) => $record && $record->url),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                ViewColumn::make('full_name')
                    ->view('tables.columns.github-repo-badge')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Repository'),

                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('language.name')
                    ->badge()
                    ->toggleable()
                    ->color(fn (string $state): string => match ($state) {
                        'PHP' => 'purple',
                        'JavaScript' => 'yellow',
                        'TypeScript' => 'blue',
                        'Python' => 'green',
                        default => 'gray',
                    }),

                ToggleColumn::make('private'),

                TextColumn::make('stars_count')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('forks_count')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('open_issues_count')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('last_push_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('last_push_at', 'desc')
            ->actions([
                ViewAction::make()
                    ->color('gray'),
                Action::make('viewOnGithub')
                    ->label('Open in GitHub')
                    ->icon(function () {
                        return <<<'SVG'
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                        </svg>
                        SVG;
                    })
                    ->color('gray')
                    ->button()
                    ->size('sm')
                    ->outlined()
                    ->extraAttributes([
                        'class' => 'flex items-center gap-1 border-gray-700 bg-gray-800 text-gray-200 hover:bg-gray-700',
                    ])
                    ->url(fn (Repo $record) => $record->url)
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('project')
                    ->relationship('project', 'name')
                    ->preload()
                    ->label('Project')
                    ->multiple(),

                SelectFilter::make('language')
                    ->relationship('language', 'name')
                    ->preload()
                    ->label('Language')
                    ->multiple(),

                TernaryFilter::make('private')
                    ->label('Privacy')
                    ->placeholder('All repositories')
                    ->trueLabel('Private repositories')
                    ->falseLabel('Public repositories'),

                Filter::make('stars')
                    ->form([
                        Select::make('stars_operator')
                            ->options([
                                '>=' => 'At least',
                                '<=' => 'At most',
                            ])
                            ->default('>='),
                        TextInput::make('stars_count')
                            ->numeric()
                            ->default(100),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['stars_count'],
                                fn (Builder $query, $count): Builder => $query->where(
                                    'stars_count',
                                    $data['stars_operator'],
                                    $count
                                ),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['stars_count']) {
                            return null;
                        }

                        return 'Stars ' . $data['stars_operator'] . ' ' . $data['stars_count'];
                    }),

                Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('last_push_at', '>=', now()->subMonths(3)))
                    ->label('Recently Active')
                    ->toggle(),

                Filter::make('updated_at')
                    ->form([
                        DateTimePicker::make('last_push_from'),
                        DateTimePicker::make('last_push_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['last_push_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_push_at', '>=', $date),
                            )
                            ->when(
                                $data['last_push_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_push_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['last_push_from'] ?? null) {
                            $indicators[] = 'Last pushed from ' . carbon($data['last_push_from'])->toFormattedDateString();
                        }

                        if ($data['last_push_until'] ?? null) {
                            $indicators[] = 'Last pushed until ' . carbon($data['last_push_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->filtersFormColumns(3)
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListRepos::route('/'),
            'create' => CreateRepo::route('/create'),
            'edit' => EditRepo::route('/{record}/edit'),
            'view' => ViewRepo::route('/{record}'),
        ];
    }
}
