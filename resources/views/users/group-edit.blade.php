<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Group') }}: {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <x-mary-alert icon="o-check-circle" class="alert-success" dismissible>
                    {{ session('success') }}
                </x-mary-alert>
            @endif

            @if (session('error'))
                <x-mary-alert icon="o-x-circle" class="alert-error" dismissible>
                    {{ session('error') }}
                </x-mary-alert>
            @endif

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('groups.edit', $group->id) }}?tab=details" 
                       class="border-transparent {{ request('tab', 'details') === 'details' ? 'border-yellow-500 text-yellow-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Group Details
                    </a>
                    @can('manageMembers', $group)
                        <a href="{{ route('groups.edit', $group->id) }}?tab=members" 
                           class="border-transparent {{ request('tab') === 'members' ? 'border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Manage Members
                        </a>
                        @php
                            $pendingRequests = \App\Models\GroupJoinRequest::where('group_id', $group->id)
                                ->where('status', 'pending')
                                ->with(['user'])
                                ->orderBy('created_at', 'desc')
                                ->get();
                        @endphp
                        <a href="{{ route('groups.edit', $group->id) }}?tab=requests" 
                           class="border-transparent {{ request('tab') === 'requests' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Pending Requests
                            @if($pendingRequests->count() > 0)
                                <span class="ml-2 bg-red-100 text-red-800 py-0.5 px-2 rounded-full text-xs font-medium">
                                    {{ $pendingRequests->count() }}
                                </span>
                            @endif
                        </a>
                    @endcan
                </nav>
            </div>

            <!-- Tab Content -->
            @if(request('tab', 'details') === 'details')
                <!-- Group Details Tab Content -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Edit Group Information</h3>
                    <form method="POST" action="{{ route('groups.update', $group->id) }}">
                    <form method="POST" action="{{ route('groups.update', $group->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Group Name <span class="text-red-500">*</span></label>
                                <input 
                                    type="text"
                                    name="name" 
                                    value="{{ old('name', $group->name) }}"
                                    placeholder="Enter group name"
                                    required
                                    class="input input-bordered w-full @error('name') input-error @enderror"
                                />
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea 
                                    name="description" 
                                    rows="4"
                                    placeholder="Enter group description (optional)"
                                    class="textarea textarea-bordered w-full @error('description') textarea-error @enderror"
                                >{{ old('description', $group->description) }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="btn btn-warning">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Update Group
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-ghost">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            
            @elseif(request('tab') === 'members')
                <!-- Manage Members Tab Content -->
                @can('manageMembers', $group)
                    <x-mary-card title="Group Members" shadow separator>
                    
                    <!-- Add Member Form -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold mb-3">Add New Member</h3>
                        <form method="POST" action="{{ route('groups.members.add', $group->id) }}" class="flex gap-3">
                            @csrf
                            
                            <select name="user_id" class="select select-bordered flex-1" required>
                                <option value="">Select User</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            
                            <select name="role_id" class="select select-bordered flex-1" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    @if($role->level <= auth()->user()->groups()->where('group_id', $group->id)->first()?->pivot->role?->level)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Member
                            </button>
                        </form>
                    </div>

                    <!-- Members List -->
                    <div class="space-y-3">
                        @forelse($group->members()->with('user', 'role')->get() as $membership)
                            <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar with role color -->
                                    <div class="avatar placeholder">
                                        <div class="w-12 h-12 rounded-full text-white" style="background-color: {{ $membership->role->badge_color }}">
                                            <span class="text-lg">{{ strtoupper(substr($membership->user->name, 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <p class="font-semibold">{{ $membership->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $membership->user->email }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="badge" style="background-color: {{ $membership->role->badge_color }}; color: white;">
                                                {{ $membership->role->name }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                Joined {{ $membership->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <!-- Change Role Button -->
                                    <button 
                                        type="button"
                                        onclick="change_role_modal_{{ $membership->id }}.showModal()"
                                        class="btn btn-sm btn-ghost"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Change Role
                                    </button>

                                    <!-- Change Role Modal -->
                                    <dialog id="change_role_modal_{{ $membership->id }}" class="modal">
                                        <div class="modal-box">
                                            <h3 class="font-bold text-lg mb-4">Change Role for {{ $membership->user->name }}</h3>
                                            <form method="POST" action="{{ route('groups.members.update-role', [$group->id, $membership->user_id]) }}">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium mb-2">New Role</label>
                                                    <select name="role_id" class="select select-bordered w-full" required>
                                                        @foreach($roles as $role)
                                                            @if($role->level <= auth()->user()->groups()->where('group_id', $group->id)->first()?->pivot->role?->level)
                                                                <option value="{{ $role->id }}" {{ $membership->role_id == $role->id ? 'selected' : '' }}>
                                                                    {{ $role->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="modal-action">
                                                    <button type="submit" class="btn btn-warning">Update Role</button>
                                                    <button type="button" class="btn btn-ghost" onclick="change_role_modal_{{ $membership->id }}.close()">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                        <form method="dialog" class="modal-backdrop"><button>close</button></form>
                                    </dialog>

                                    <!-- Remove Member Button -->
                                    <form method="POST" action="{{ route('groups.members.remove', [$group->id, $membership->user_id]) }}" 
                                          onsubmit="return confirm('Are you sure you want to remove {{ $membership->user->name }} from this group?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error btn-outline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p>No members yet</p>
                            </div>
                        @endforelse
                    </div>
                </x-mary-card>
                @endcan

            @elseif(request('tab') === 'requests')
                <!-- Pending Requests Tab Content -->
                @can('manageMembers', $group)
                    <x-mary-card title="Pending Join Requests" shadow separator>
                        @php
                            $pendingRequests = \App\Models\GroupJoinRequest::where('group_id', $group->id)
                                ->where('status', 'pending')
                                ->with('user')
                                ->orderBy('created_at', 'desc')
                                ->get();
                        @endphp

                        <div class="space-y-4">
                            @forelse($pendingRequests as $request)
                                <div class="p-4 border-2 border-yellow-300 bg-yellow-50 rounded-lg">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3 flex-1">
                                            <!-- User Avatar -->
                                            <div class="avatar placeholder">
                                                <div class="w-12 h-12 rounded-full bg-yellow-500 text-white">
                                                    <span class="text-lg">{{ strtoupper(substr($request->user->name, 0, 1)) }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex-1">
                                                <p class="font-semibold text-lg">{{ $request->user->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $request->user->email }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Requested {{ $request->created_at->diffForHumans() }}</p>
                                                
                                                @if($request->message)
                                                    <div class="mt-2 p-2 bg-white rounded border">
                                                        <p class="text-sm"><strong>Message:</strong> {{ $request->message }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex gap-2 ml-4">
                                            <!-- Accept Button -->
                                            <button 
                                                type="button"
                                                onclick="accept_request_modal_{{ $request->id }}.showModal()"
                                                class="btn btn-sm btn-success"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Accept
                                            </button>

                                            <!-- Accept Modal -->
                                            <dialog id="accept_request_modal_{{ $request->id }}" class="modal">
                                                <div class="modal-box">
                                                    <h3 class="font-bold text-lg mb-4">Accept Join Request</h3>
                                                    <p class="mb-4">Approve <strong>{{ $request->user->name }}</strong> to join this group.</p>
                                                    
                                                    <form method="POST" action="{{ route('requests.approve', $request->id) }}">
                                                        @csrf
                                                        
                                                        <div class="mb-4">
                                                            <label class="block text-sm font-medium mb-2">Assign Role <span class="text-red-500">*</span></label>
                                                            <select name="role_id" class="select select-bordered w-full" required>
                                                                <option value="">Select a role</option>
                                                                @foreach($roles as $role)
                                                                    @if($role->level <= auth()->user()->groups()->where('group_id', $group->id)->first()?->pivot->role?->level)
                                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="mb-4">
                                                            <label class="block text-sm font-medium mb-2">Welcome Message (Optional)</label>
                                                            <textarea 
                                                                name="admin_message" 
                                                                rows="3"
                                                                placeholder="Add a welcome message..."
                                                                class="textarea textarea-bordered w-full"
                                                            ></textarea>
                                                        </div>

                                                        <div class="modal-action">
                                                            <button type="submit" class="btn btn-success">Approve & Add Member</button>
                                                            <button type="button" class="btn btn-ghost" onclick="accept_request_modal_{{ $request->id }}.close()">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <form method="dialog" class="modal-backdrop"><button>close</button></form>
                                            </dialog>

                                            <!-- Reject Button -->
                                            <button 
                                                type="button"
                                                onclick="reject_request_modal_{{ $request->id }}.showModal()"
                                                class="btn btn-sm btn-error btn-outline"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Reject
                                            </button>

                                            <!-- Reject Modal -->
                                            <dialog id="reject_request_modal_{{ $request->id }}" class="modal">
                                                <div class="modal-box">
                                                    <h3 class="font-bold text-lg mb-4">Reject Join Request</h3>
                                                    <p class="mb-4">Are you sure you want to reject <strong>{{ $request->user->name }}</strong>'s request?</p>
                                                    
                                                    <form method="POST" action="{{ route('requests.reject', $request->id) }}">
                                                        @csrf
                                                        
                                                        <div class="mb-4">
                                                            <label class="block text-sm font-medium mb-2">Reason (Optional)</label>
                                                            <textarea 
                                                                name="admin_message" 
                                                                rows="3"
                                                                placeholder="Optionally provide a reason for rejection..."
                                                                class="textarea textarea-bordered w-full"
                                                            ></textarea>
                                                        </div>

                                                        <div class="modal-action">
                                                            <button type="submit" class="btn btn-error">Reject Request</button>
                                                            <button type="button" class="btn btn-ghost" onclick="reject_request_modal_{{ $request->id }}.close()">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <form method="dialog" class="modal-backdrop"><button>close</button></form>
                                            </dialog>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p>No pending requests</p>
                                </div>
                            @endforelse
                        </div>
                    </x-mary-card>
                @endcan
            @endif
        </div>
    </div>
</x-app-layout>
