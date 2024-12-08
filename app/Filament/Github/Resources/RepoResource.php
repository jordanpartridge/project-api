<?php

namespace App\Filament\Github\Resources;

use App\Filament\Github\Resources\RepoResource\Pages\CreateRepo;
use App\Filament\Github\Resources\RepoResource\Pages\EditRepo;
use App\Filament\Github\Resources\RepoResource\Pages\ListRepos;
use App\Filament\Github\Resources\RepoResource\Pages\ViewRepo;
use App\Filament\Github\Resources\RepoResource\RelationManagers\CommitsRelationshipManager;
use App\Models\Repo;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RepoResource extends Resource
{
    protected static ?string $model = Repo::class;
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationGroup = 'GitHub';
    protected static ?string $navigationLabel = 'Repositories';
    protected static ?string $modelLabel = 'Repository';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Core Details')
                    ->schema([
                        TextInput::make('github_id')
                            ->required()
                            ->numeric()
                            ->label('GitHub ID'),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('full_name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Toggle::make('private')
                            ->default(false),

                        Select::make('project_id')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable(),
                    ])
                    ->columns(2),

                Section::make('Repository Information')
                    ->schema([
                        Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Select::make('language_id')
                            ->relationship('language', 'name')
                            ->nullable()
                            ->searchable(),

                        TextInput::make('default_branch')
                            ->default('main')
                            ->maxLength(255),

                        TextInput::make('license')
                            ->maxLength(255)
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Statistics')
                    ->schema([
                        TextInput::make('stars_count')
                            ->numeric()
                            ->readonly()
                            ->default(0),

                        TextInput::make('forks_count')
                            ->numeric()
                            ->default(0)
                            ->readonly(),

                        TextInput::make('open_issues_count')
                            ->numeric()
                            ->default(0)
                            ->readonly(),

                        DateTimePicker::make('last_push_at')
                            ->nullable()
                            ->readonly(),
                    ])
                    ->columns(2),

                Section::make('Additional Details')
                    ->schema([
                        TagsInput::make('topics')
                            ->separator(',')
                            ->helperText('Enter topics and press Enter')
                            ->nullable(),
                    ])
                    ->collapsed(),

                View::make('filament.forms.components.github-stats')
                    ->visible(fn ($record) => $record && $record->url),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('owner.avatar_url')
                    ->label('Owner')
                    ->circular()
                    ->size(40)
                    ->url(fn (Repo $record): string => OwnerResource::getUrl('view', ['record' => $record->owner]))
                    ->alignCenter(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-code-bracket')
                    ->description(fn (Repo $record): string => $record->full_name),

                TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->wrap()
                    ->toggleable()
                    ->tooltip(fn (TextColumn $column): ?string => strlen($column->getState()) > 50 ? $column->getState() : null
                    ),

                TextColumn::make('language.name')
                    ->badge()
                    ->colors([
                        'purple' => 'PHP',
                        'yellow' => 'JavaScript',
                        'blue' => 'TypeScript',
                        'green' => 'Python',
                        'gray' => 'default',
                    ]),

                IconColumn::make('private')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('stars_count')
                    ->numeric()
                    ->sortable()
                    ->label('Stars')
                    ->icon('heroicon-m-star'),

                TextColumn::make('last_push_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Push')
                    ->icon('heroicon-m-clock'),
            ])
            ->defaultSort('last_push_at', 'desc')
            ->filters([
                SelectFilter::make('project')
                    ->relationship('project', 'name')
                    ->preload()
                    ->multiple(),

                SelectFilter::make('language')
                    ->relationship('language', 'name')
                    ->preload()
                    ->multiple(),

                TernaryFilter::make('private')
                    ->label('Privacy')
                    ->placeholder('All repositories')
                    ->trueLabel('Private repositories')
                    ->falseLabel('Public repositories'),

                Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('last_push_at', '>=', now()->subMonths(3)))
                    ->label('Recently Active')
                    ->toggle(),
            ])
            ->actions([
                ViewAction::make()
                    ->color('gray'),

                Action::make('viewOnGithub')
                    ->label('Open in GitHub')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->button()
                    ->url(fn (Repo $record) => $record->url)
                    ->openUrlInNewTab(),

                EditAction::make()
                    ->color('gray'),
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
            CommitsRelationshipManager::make(),
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
