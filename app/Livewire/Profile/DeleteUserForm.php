<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DeleteUserForm extends Component
{
    public $password = '';
    public $confirmingUserDeletion = false;

    public function confirmUserDeletion()
    {
        $this->confirmingUserDeletion = true;
        $this->password = '';
    }

    public function deleteUser()
    {
        $this->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        
        Auth::logout();
        
        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    public function cancelUserDeletion()
    {
        $this->confirmingUserDeletion = false;
        $this->password = '';
    }

    public function render()
    {
        return view('livewire.profile.delete-user-form');
    }
}
