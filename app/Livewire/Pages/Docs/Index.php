<?php

namespace App\Livewire\Pages\Docs;

use App\Models\Documentation;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $selectedCategory = null;

    protected $rules = [
        'selectedCategory' => 'nullable|string|exists:documentation,category'
    ];

    public function mount($category = null)
    {
        $this->selectedCategory = $category;
    }

    public function getDocsProperty()
    {
        return Documentation::published()
            ->when($this->selectedCategory, fn($query) => 
                $query->where('category', $this->selectedCategory)
            )
            ->orderBy('order')
            ->paginate(10);
    }

    public function getCategoriesProperty()
    {
        return Documentation::published()
            ->select('category')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('category')
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.docs.index', [
            'docs' => $this->docs,
            'categories' => $this->categories
        ]);
    }
}