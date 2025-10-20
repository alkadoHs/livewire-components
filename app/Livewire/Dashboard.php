<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public ?array $selectedUsers;

    public function saveUsers()
    {
        dd($this->selectedUsers);
    }
    
    public function render()
    {
        return view('livewire.dashboard');
    }
}
