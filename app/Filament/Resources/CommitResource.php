<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommitResource\Pages\CreateCommit;
use App\Filament\Resources\CommitResource\Pages\EditCommit;
use App\Filament\Resources\CommitResource\Pages\ListCommits;
use App\Models\Commit;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CommitResource extends Resource
{
    protected static ?string $model = Commit::class;
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';
    protected static ?string $navigationGroup = 'GitHub';
    protected static ?string $navigationLabel = 'Commits';
    protected static ?string $modelLabel = 'Commit';

    private const COMMIT_SVG_ICON = <<<'HTML'
        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd" d="M8 1.5a6.5 6.5 0 100 13 6.5 6.5 0 000-13zM0 8a8 8 0 1116 0A8 8 0 010 8z" clip-rule="evenodd"/>
        </svg>
    HTML;

    private const EXTERNAL_LINK_SVG = <<<'HTML'
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
    HTML;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Repository Column with ViewColumn preserved
                ViewColumn::make('full_name')
                    ->view('tables.columns.github-repo-badge')
                    ->state(function (Commit $record): array {
                        return [
                            'name' => $record->repo->full_name,
                            'url' => route('filament.admin.resources.repos.view', ['record' => $record->repo]),
                        ];
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Repository'),

                // Commit Details Column with ViewColumn preserved
                TextColumn::make('message')
                    ->label('Commit')
                    ->formatStateUsing(fn ($state, $record) => view('filament.tables.columns.commit-details', [
                        'sha' => Str::limit($record->sha, 7),
                        'message' => $state,
                        'authorName' => $record->author['name'],
                        'authorEmail' => $record->author['email'],
                        'date' => Carbon::parse($record->author['date'])->diffForHumans(),
                    ]))
                    ->html()
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->size('sm')  // Make text slightly smaller for better readability
                    ->extraAttributes(['class' => 'space-y-1']),  // Add spacing between elements

                // Author Column with ViewColumn preserved
                TextColumn::make('author.name')
                    ->label('Author')
                    ->formatStateUsing(fn ($state, $record) => view('filament.tables.columns.author-details', [
                        'name' => $state,
                        'email' => $record->author['email'],
                        'date' => Carbon::parse($record->author['date'])->diffForHumans(),
                    ]))
                    ->html()
                    ->sortable()
                    ->searchable(['author.name', 'author.email'])
                    ->toggleable()
                    ->alignment('left'),  // Ensure consistent alignment

                // Date Column
                TextColumn::make('author.date')
                    ->label('Committed')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->description(fn ($record) => Carbon::parse($record->author['date'])->diffForHumans())
                    ->alignment('left')  // Consistent alignment
                    ->size('sm'),  // Smaller text for better visual hierarchy
            ])
            ->actions([
                Action::make('openInGithub')
                    ->label('Open in GitHub')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Commit $record) => "https://github.com/{$record->repo->full_name}/commit/{$record->sha}")
                    ->openUrlInNewTab()
                    ->button(),  // Make it a button for better visibility

                EditAction::make()
                    ->color('gray')
                    ->icon('heroicon-m-pencil-square'),

                Action::make('details')
                    ->label('View Details')
                    ->icon('heroicon-m-information-circle')
                    ->color('gray')
                    ->modalContent(fn (Commit $record) => view('filament.resources.commit-resource.details', [
                        'commit' => $record,
                    ])),
            ])
            ->filters([
                SelectFilter::make('repo')
                    ->relationship('repo', 'full_name')
                    ->preload()
                    ->searchable()
                    ->label('Repository'),

                SelectFilter::make('time_range')
                    ->label('Time Period')
                    ->options([
                        'today' => 'Today',
                        'week' => 'This Week',
                        'month' => 'This Month',
                        'quarter' => 'This Quarter',
                        'year' => 'This Year',
                        'historic' => 'Historic (> 1 year)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value']) {
                            'today' => $query->whereDate('author->date', Carbon::today()),
                            'week' => $query->whereBetween('author->date', [
                                Carbon::now()->startOfWeek(),
                                Carbon::now()->endOfWeek(),
                            ]),
                            'month' => $query->whereBetween('author->date', [
                                Carbon::now()->startOfMonth(),
                                Carbon::now()->endOfMonth(),
                            ]),
                            'quarter' => $query->whereBetween('author->date', [
                                Carbon::now()->startOfQuarter(),
                                Carbon::now()->endOfQuarter(),
                            ]),
                            'year' => $query->whereYear('author->date', Carbon::now()->year),
                            'historic' => $query->whereDate('author->date', '<', Carbon::now()->subYear()),
                            default => $query
                        };
                    }),
            ])
            ->striped()
            ->defaultSort('author.date', 'desc')  // Sort by most recent commits by default
            ->defaultGroup('repo.full_name')  // Group by repository by default
            ->paginated([10, 25, 50, 100])
            ->searchable();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Commit Information')
                    ->schema([
                        Select::make('repo_id')
                            ->relationship('repo', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(['md' => 2]),

                        TextInput::make('sha')
                            ->label('SHA')
                            ->required()
                            ->columnSpan(['md' => 1])
                            ->formatStateUsing(fn ($state) => Str::limit($state, 7))
                            ->disabled(),

                        Textarea::make('message')
                            ->label('Commit Message')
                            ->required()
                            ->rows(3)
                            ->placeholder('Enter the commit message')
                            ->columnSpanFull(),

                        Section::make('Author Information')
                            ->schema([
                                TextInput::make('author.name')
                                    ->label('Author Name')
                                    ->required(),

                                TextInput::make('author.email')
                                    ->label('Author Email')
                                    ->email()
                                    ->required(),

                                DateTimePicker::make('author.date')
                                    ->label('Commit Date')
                                    ->seconds(false)
                                    ->timezone('UTC')
                                    ->required(),
                            ])
                            ->columns(3),
                    ])
                    ->columns(['md' => 3]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommits::route('/'),
            'create' => CreateCommit::route('/create'),
            'edit' => EditCommit::route('/{record}/edit'),
        ];
    }

    private static function getTimePeriod(Commit $commit): string
    {
        $date = Carbon::parse($commit->author['date']);
        $now = Carbon::now();

        if ($date->isToday()) {
            return 'Today';
        }

        if ($date->isCurrentWeek()) {
            return 'This Week';
        }

        if ($date->isCurrentMonth()) {
            return 'This Month';
        }

        if ($date->isCurrentQuarter()) {
            return 'This Quarter';
        }

        if ($date->isCurrentYear()) {
            return 'This Year';
        }

        return 'Historic';
    }

    private static function getRepoNameColumn(): ViewColumn
    {
        return ViewColumn::make('full_name')
            ->view('tables.columns.github-repo-badge')
            ->state(function (Commit $record): array {
                return [
                    'name' => $record->repo->full_name,
                    'url' => route('filament.admin.resources.repos.view', ['record' => $record->repo]),
                ];
            })
            ->searchable()
            ->sortable()
            ->toggleable()
            ->label('Repository');
    }

    private static function getCommitColumn(): TextColumn
    {
        return TextColumn::make('message')
            ->label('Commit')
            ->formatStateUsing(fn ($state, $record) => sprintf(
                '<div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <code class="px-2 py-1 bg-gray-800 rounded-md text-gray-300 text-xs">%s</code>
                    </div>
                    <div class="text-gray-400 text-sm">%s</div>
                </div>',
                e(Str::limit($record->sha, 7)),
                e($state)
            ))
            ->html()
            ->sortable()
            ->searchable()
            ->wrap();
    }

    private static function getAuthorColumn(): TextColumn
    {
        return TextColumn::make('author.name')
            ->label('Author')
            ->formatStateUsing(fn ($state, $record) => sprintf(
                '<div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-400">%s</span>
                    <span class="text-xs text-gray-500">(%s)</span>
                </div>',
                e($state),
                e($record->author['email'])
            ))
            ->html()
            ->sortable()
            ->searchable(['author.name', 'author.email'])
            ->toggleable();
    }

    private static function getCommitDateColumn(): TextColumn
    {
        return TextColumn::make('author.date')
            ->label('Committed')
            ->dateTime()
            ->sortable()
            ->toggleable()
            ->description(fn ($record) => Carbon::parse($record->author['date'])->diffForHumans());
    }

    private static function getGithubAction(): Action
    {
        return Action::make('openInGithub')
            ->label('Open in GitHub')
            ->icon('heroicon-m-arrow-top-right-on-square')
            ->color('gray')
            ->button()
            ->url(fn (Commit $record) => "https://github.com/{$record->repo->full_name}/commit/{$record->sha}")
            ->openUrlInNewTab();
    }

    private static function getEditAction(): EditAction
    {
        return EditAction::make()
            ->icon('heroicon-m-pencil-square');
    }

    private static function getRepoFilter(): SelectFilter
    {
        return SelectFilter::make('repo')
            ->relationship('repo', 'full_name')
            ->preload()
            ->searchable();
    }
}
