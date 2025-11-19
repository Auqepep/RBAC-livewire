<?php

namespace App\Livewire\User;

use App\Models\Group;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AvailableGroups extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        // Debug: Check total groups
        $totalGroups = Group::count();
        $activeGroups = Group::where('is_active', true)->count();
        
        // Get groups user is not a member of
        $userGroupIds = $user->groups()->pluck('groups.id')->toArray();
        
        $query = Group::where('is_active', true);
        
        // Only exclude groups user is already a member of if they have groups
        if (!empty($userGroupIds)) {
            $query->whereNotIn('id', $userGroupIds);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $groups = $query->withCount(['members'])
            ->latest()
            ->paginate(9);

        // Add debug info to session for testing
        session()->flash('debug', "Total groups: $totalGroups, Active: $activeGroups, User groups: " . count($userGroupIds) . ", Available: " . $groups->total());

        return view('livewire.user.available-groups', [
            'groups' => $groups,
        ]);
    }
}
