<?php

namespace App\Livewire\User;

use App\Models\Group;
use App\Models\GroupJoinRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AvailableGroups extends Component
{
    use WithPagination;

    public $search = '';
    public $requestMessage = '';
    public $selectedGroupId = null;
    public $showRequestModal = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openRequestModal($groupId)
    {
        $this->selectedGroupId = $groupId;
        $this->requestMessage = '';
        $this->showRequestModal = true;
    }

    public function closeRequestModal()
    {
        $this->showRequestModal = false;
        $this->selectedGroupId = null;
        $this->requestMessage = '';
    }

    public function requestToJoin()
    {
        $this->validate([
            'requestMessage' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        
        // Check if user already has a pending or approved request
        $existingRequest = GroupJoinRequest::where('user_id', $user->id)
            ->where('group_id', $this->selectedGroupId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRequest) {
            session()->flash('error', 'You already have a request for this group.');
            $this->closeRequestModal();
            return;
        }

        // Check if user is already a member
        $group = Group::find($this->selectedGroupId);
        if ($group->hasMember($user->id)) {
            session()->flash('error', 'You are already a member of this group.');
            $this->closeRequestModal();
            return;
        }

        // Create the join request
        GroupJoinRequest::create([
            'user_id' => $user->id,
            'group_id' => $this->selectedGroupId,
            'message' => $this->requestMessage,
            'status' => 'pending'
        ]);

        session()->flash('message', 'Your request to join the group has been sent to administrators.');
        $this->closeRequestModal();
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

        // Get user's pending requests
        $userPendingRequests = GroupJoinRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('group_id')
            ->toArray();

        // Add debug info to session for testing
        session()->flash('debug', "Total groups: $totalGroups, Active: $activeGroups, User groups: " . count($userGroupIds) . ", Available: " . $groups->total());

        return view('livewire.user.available-groups', [
            'groups' => $groups,
            'userPendingRequests' => $userPendingRequests
        ]);
    }
}
