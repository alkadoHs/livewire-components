<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User; // Make sure you have a User model

class EditTask extends Component
{
    public $assignedUserId;

    public function mount($user)
    {
        // Simulate loading an existing task from the database
        // that is assigned to the user with ID = 5.
        $this->assignedUserId = $user; 
    }

    public function save()
    {
        // Your logic to save the task with the new assigned user
        // For example:
        // Task::find($this->taskId)->update(['user_id' => $this->assignedUserId]);
        
        session()->flash('message', 'Task assigned to user ID: ' . $this->assignedUserId);
    }

    public function render()
    {
        return view('livewire.edit-task');
    }
}