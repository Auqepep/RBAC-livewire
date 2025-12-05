<?php

namespace App\Livewire\Layout;

use Livewire\Component;
use App\Livewire\Actions\Logout;

class Navigation extends Component
{
    public function logout(Logout $logout)
    {
        $logout();
        return $this->redirect('/', navigate: true);
    }
    
    public function render()
    {
        return view('livewire.layout.navigation');
    }
}