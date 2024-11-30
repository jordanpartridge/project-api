<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommitResource\Pages\CreateCommit;
use App\Filament\Resources\CommitResource\Pages\EditCommit;
use App\Filament\Resources\CommitResource\Pages\ListCommits;
use App\Models\Commit;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CommitResource extends Resource
{
    protected static ?string $model = Commit::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';

    protected static ?string $navigationGroup = 'GitHub';

    protected static ?string $navigationLabel = 'Commits';

    protected static ?string $modelLabel = 'Commit';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Commit Details')
                    ->schema([
                        Select::make('repo_id')
                            ->relationship('repo', 'full_name')
                            ->required()
                            ->searchable(),

                        TextInput::make('sha')
                            ->required()
                            ->maxLength(40)
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->required()
                            ->maxLength(65535)
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('author')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ViewColumn::make('repo.full_name')
                    ->view('tables.columns.github-repo-badge')
                    ->state(function ($record): array {
                        return [
                            'name' => $record->repo->full_name,
                            'url' => route('filament.admin.resources.repos.view', $record->repo),
                        ];
                    }
                    )
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Repository'),

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

                        return mb_strlen($state) > 50 ? $state : null;
                    }),

                ViewColumn::make('author')
                    ->view('tables.columns.github-author')
                    ->state(function (Commit $commit): array {
                        return [
                            'name' => $commit->author['login'],
                            'email' => $commit->author['html_url'],
                            'date' => now(),
                        ];
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('id', 'desc')
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
                    ->url(fn (Commit $record) => $record->url)
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('repo')
                    ->relationship('repo', 'full_name')
                    ->preload(),
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
            //
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
