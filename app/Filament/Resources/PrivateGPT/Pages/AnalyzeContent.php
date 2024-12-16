<?php

namespace App\Filament\Resources\PrivateGPT\Pages;

use App\Filament\Resources\PrivateGPT\PrivateGPTResource;
use App\Services\PrivateGPT\PrivateGPTService;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Livewire\Attributes\Computed;

class AnalyzeContent extends Page
{
    protected static string $resource = PrivateGPTResource::class;

    protected static string $view = 'filament.resources.private-gpt.pages.analyze-content';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('content')
                    ->required()
                    ->maxLength(65535)
                    ->label('Content to Analyze'),
            ])
            ->statePath('data');
    }

    public function analyze(): void
    {
        $data = $this->form->getState();

        $service = app(PrivateGPTService::class);
        
        $analysis = $service->analyze(
            $data['content'],
            null,
            ['name' => $data['name']]
        );

        $this->redirect(PrivateGPTResource::getUrl('edit', ['record' => $analysis]));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('analyze')
                ->label('Analyze Content')
                ->action('analyze')
                ->color('primary'),
        ];
    }
}
