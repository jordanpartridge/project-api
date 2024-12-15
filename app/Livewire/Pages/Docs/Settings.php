<?php

namespace App\Livewire\Pages\Docs;

use Livewire\Component;
use Illuminate\Validation\Rule;

class Settings extends Component
{
    public $title;
    public $content;
    public $category;
    public $order;
    public $is_published = false;

    protected $rules = [
        'title' => 'required|string|max:255|unique:documentation,title',
        'content' => 'required|string',
        'category' => ['required', 'string', Rule::in(['guides', 'tutorials', 'api', 'examples'])],
        'order' => 'required|integer|min:0',
        'is_published' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        // Create the documentation entry
        Documentation::create([
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category,
            'order' => $this->order,
            'is_published' => $this->is_published,
        ]);

        session()->flash('message', 'Documentation created successfully.');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.pages.docs.settings');
    }
}
