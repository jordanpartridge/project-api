<?php

namespace App\Livewire\Pages\Docs;

use Livewire\Component;

class Show extends Component
{
    public $slug;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    public function render()
    {
        return view('livewire.pages.docs.show');
    }
}
