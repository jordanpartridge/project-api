<?php

namespace App\Livewire\Pages\Docs;

use App\Models\Documentation;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $selectedCategory = null;

    protected $queryString = ['category'];

    protected $rules = [
        'selectedCategory' => ['nullable', 'string', Rule::in([
            Documentation::CATEGORY_GUIDES,
            Documentation::CATEGORY_TUTORIALS,
            Documentation::CATEGORY_API,
            Documentation::CATEGORY_EXAMPLES,
        ])],
    ];

    public function mount($category = null)
    {
        $this->selectedCategory = $category;
    }

    public function updatedSelectedCategory($value)
    {
        $this->resetPage();
    }

    public function render()
    {
        $docs = Documentation::query()
            ->published()
            ->when($this->selectedCategory, fn ($query) => $query->where('category', $this->selectedCategory))
            ->orderBy('order')
            ->paginate(10);

        return view('livewire.pages.docs.index', [
            'docs' => $docs,
            'categories' => Documentation::categories(),
        ]);
    }
}
