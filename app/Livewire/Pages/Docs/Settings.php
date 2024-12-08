<?php

namespace App\Livewire\Pages\Docs;

use App\Models\Documentation;
use Livewire\Component;

class Settings extends Component
{
    public $title = '';
    public $content = '';
    public $category = '';
    public $order = 0;
    public $is_published = true;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category' => 'required|string',
        'order' => 'required|integer|min:0',
        'is_published' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        Documentation::create([
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category,
            'order' => $this->order,
            'is_published' => $this->is_published,
        ]);

        $this->reset();
        $this->dispatch('documentation-saved');
    }

    public function render()
    {
        return view('livewire.pages.docs.settings');
    }
}
