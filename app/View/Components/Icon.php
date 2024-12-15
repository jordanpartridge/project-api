<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\View;

class Icon extends Component
{
    public function __construct(
        public string $name,
        public ?string $class = null,
        public ?string $fallback = 'default'
    ) {}

    public function render()
    {
        $viewName = "icons.{$this->name}";
        
        if (!View::exists($viewName)) {
            $viewName = "icons.{$this->fallback}";
        }

        return view($viewName, [
            'attributes' => $this->attributes->merge([
                'class' => $this->class
            ])
        ]);
    }
}