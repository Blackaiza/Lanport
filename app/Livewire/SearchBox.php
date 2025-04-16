<?php

namespace App\Livewire;

use Livewire\Component;

use function Laravel\Prompts\search;

class SearchBox extends Component
{
    public $search = '';

    //Ini hook cycle
    public function updatedSearch()
    {
        $this->dispatch('search', search: $this->search);
    }

    //Sama je macam yang hook cycle tadi
    public function update()
    {
        $this->dispatch('search', search: $this->search);
    }

    public function render()
    {
        return view('livewire.search-box');
    }
}
