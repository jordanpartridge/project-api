<?php

namespace App\Livewire\Pages\Docs;

use App\Models\Documentation;
use Livewire\Component;

class Settings extends Component
{
    public $title;
    public $content;
    public $category;
    public $order;
    public $is_published = true;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category' => 'required|string',
        'order' => 'required|integer|min:1',
    ];

    public function save()
    {
        $validated = $this->validate();

        Documentation::create([
            ...$validated,
            'is_published' => $this->is_published,
        ]);

        $this->reset();
    }

    public function render()
    {
        return view('livewire.pages.docs.settings');
    }
}
