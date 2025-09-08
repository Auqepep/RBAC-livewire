<?php

namespace App\Livewire\Admin;

use App\Models\GroupJoinRequest;
use App\Models\GroupMember;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class GroupJoinRequests extends Component
{
    use WithPagination;

    public $filter = 'pending'; // pending, approved, rejected, all
    public $search = '';
    public $selectedRequestId = null;
    public $adminMessage = '';
    public $showResponseModal = false;
    public $responseAction = ''; // approve or reject

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function openResponseModal($requestId, $action)
    {
        $this->selectedRequestId = $requestId;
        $this->responseAction = $action;
        $this->adminMessage = '';
        $this->showResponseModal = true;
    }

    public function closeResponseModal()
    {
        $this->showResponseModal = false;
        $this->selectedRequestId = null;
        $this->responseAction = '';
        $this->adminMessage = '';
    }

    public function quickApprove($requestId)
    {
        $this->processRequest($requestId, 'approved', '');
    }

    public function quickReject($requestId)
    {
        $this->processRequest($requestId, 'rejected', '');
    }

    public function processResponse()
    {
        $this->validate([
            'adminMessage' => 'nullable|string|max:500',
        ]);

        $status = $this->responseAction === 'approve' ? 'approved' : 'rejected';
        $this->processRequest($this->selectedRequestId, $status, $this->adminMessage);
        $this->closeResponseModal();
    }

    private function processRequest($requestId, $status, $adminMessage)
    {
        $request = GroupJoinRequest::find($requestId);
        
        if (!$request || $request->status !== 'pending') {
            session()->flash('error', 'Request not found or already processed.');
            return;
        }

        $request->update([
            'status' => $status,
            'reviewed_by' => Auth::id(),
            'admin_message' => $adminMessage,
            'reviewed_at' => now()
        ]);

        // If approved, add user to group
        if ($status === 'approved') {
            $group = $request->group;
            $group->addMember($request->user_id, Auth::id());
            
            session()->flash('message', 'Request approved and user added to group successfully.');
        } else {
            session()->flash('message', 'Request rejected successfully.');
        }
    }

    public function render()
    {
        $query = GroupJoinRequest::with(['user', 'group', 'reviewer']);

        // Apply status filter
        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        // Apply search
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })->orWhereHas('group', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        $requests = $query->latest()->paginate(10);

        return view('livewire.admin.group-join-requests', [
            'requests' => $requests
        ]);
    }
}
