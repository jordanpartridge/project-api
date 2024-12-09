<?php

namespace App\Livewire\Pages\Docs;

use App\Models\Documentation;
use App\Services\MarkdownService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Settings extends Component
{
    public $title = '';
    public $content = '';
    public $category = '';
    public $order = 0;
    public $is_published = true;
    public $meta_data = [];

    protected $rules = [
        'title' => 'required|string|max:255|unique:documentation,title',
        'content' => 'required|string',
        'category' => ['required', 'string', Rule::in([
            Documentation::CATEGORY_GUIDES,
            Documentation::CATEGORY_TUTORIALS,
            Documentation::CATEGORY_API,
            Documentation::CATEGORY_EXAMPLES,
        ])],
        'order' => 'required|integer|min:0',
        'is_published' => 'boolean',
        'meta_data' => 'nullable|array',
    ];

    public function save(MarkdownService $markdown)
    {
        $this->validate();

        // Convert and sanitize markdown before saving
        $sanitizedContent = $markdown->convertToHtml($this->content);

        Documentation::create([
            'title' => $this->title,
            'content' => $sanitizedContent,
            'category' => $this->category,
            'order' => $this->order,
            'is_published' => $this->is_published,
            'meta_data' => $this->meta_data,
        ]);

        $this->reset();
        $this->dispatch('documentation-saved');
    }

    public function render()
    {
        return view('livewire.pages.docs.settings', [
            'categories' => Documentation::categories(),
        ]);
    }
}
