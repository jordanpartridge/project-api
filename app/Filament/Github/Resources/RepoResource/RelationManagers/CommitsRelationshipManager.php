<?php

namespace App\Filament\Github\Resources\RepoResource\RelationManagers;

use App\Models\Commit;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
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
                    ->maxLength(40)
                    ->label('Commit SHA')
                    ->helperText('Full SHA hash - don\'t worry, we\'ll only show the first 7 characters in the table for brevity!'),

                Textarea::make('message')
                    ->required()
                    ->maxLength(65535)
                    ->label('Commit Message')
                    ->helperText('Where the magic (or chaos) of code changes is described. Be as verbose or concise as you like!'),

                TextInput::make('author.name')
                    ->required()
                    ->maxLength(255)
                    ->label('Author Name')
                    ->helperText('Who made the commit? Give credit where credit is due!'),

                TextInput::make('author.email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->label('Author Email')
                    ->helperText('For posterity or blame, whichever comes first.'),

                DateTimePicker::make('author.date')
                    ->label('Commit Date')
                    ->seconds(false)
                    ->timezone('UTC')
                    ->required()
                    ->helperText('When did this moment of inspiration (or desperation) occur?'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
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
}
