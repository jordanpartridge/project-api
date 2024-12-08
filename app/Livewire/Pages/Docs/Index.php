<?php

namespace App\Livewire\Pages\Docs;

use App\Models\Documentation;
use Livewire\Component;

class Index extends Component
{
    public $selectedCategory = null;

    public function mount()
    {
        $this->selectedCategory = request('category');
    }

    public function render()
    {
        $docs = Documentation::query()
            ->published()
            ->when($this->selectedCategory, function ($query) {
                $query->where('category', $this->selectedCategory);
            })
            ->orderBy('order')
            ->get();

        return view('livewire.pages.docs.index', [
            'docs' => $docs,
        ]);
    }
}
