<?php

namespace App\Livewire\Pages\Docs;

use App\Models\Documentation;
use Livewire\Component;

class Index extends Component
{
    public $selectedCategory = null;

    public function render()
    {
        $query = Documentation::query()->where('is_published', true);

        if ($this->selectedCategory) {
            $query->where('category', $this->selectedCategory);
        }

        $docs = $query->get();

        return view('livewire.pages.docs.index', [
            'docs' => $docs,
        ]);
    }
}
