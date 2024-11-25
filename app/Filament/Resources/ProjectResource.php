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
use Filament\Forms\Components\View;
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
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder'; // Classic project folder

    protected static ?string $navigationLabel = 'Projects';

    protected static ?string $navigationGroup = 'Development'; // Optional: if you want to group it

    protected static ?string $modelLabel = 'Project';

    protected static ?string $pluralModelLabel = 'Projects'; // Optional: for better pluralization

    // Optional: Navigation sorting
    protected static ?int $navigationSort = 1; // Put it first in the nav

    // Optional: Add a badge to show total projects
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // Optional: Badge color
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
                            ->placeholder('Enter repo name')
                            ->columnSpanFull()
                            ->suffixAction(
                                TableAction::make('viewRepo')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->color('gray')
                                    ->url(fn ($record) => '/admin/repos/' . $record?->id)
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
                // Add a view button in the fieldset header

            ])
            ->columns(2);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => (string) $state),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->wrap(),
                ViewColumn::make('repo.full_name')
                    ->view('tables.columns.github-repo-badge')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->limit(50),
                TextColumn::make('created_at')
                    ->dateTime('d-m-Y h:i A')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                // You could add filters for repo status, stars count, etc.
            ])
            ->actions([
                ViewAction::make()
                    ->color('gray'),
                Action::make('viewOnGithub')
                    ->label('Open in GitHub')
                    ->icon('heroicon-m-code-bracket')  // or could use svg github icon
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
