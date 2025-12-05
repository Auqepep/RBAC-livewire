<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Anggota Grup: {{ $group->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-secondary btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Grup
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <x-mary-card>
                @if($errors->any())
                    <x-mary-alert icon="o-x-circle" class="alert-error mb-6">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-mary-alert>
                @endif

                <form method="POST" action="{{ route('admin.groups.update', $group) }}">
                    @csrf
                    @method('PUT')
                    
                    {{-- Hidden fields to preserve group data --}}
                    <input type="hidden" name="name" value="{{ $group->name }}">
                    <input type="hidden" name="description" value="{{ $group->description }}">
                    <input type="hidden" name="is_active" value="{{ $group->is_active ? '1' : '0' }}">
                    
                    <div class="space-y-6">
                        <!-- Group Info -->
                        <div class="bg-base-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $group->name }}</h3>
                            @if($group->description)
                                <p class="text-sm text-gray-600">{{ $group->description }}</p>
                            @endif
                        </div>

                        <!-- Group Members Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Kelola Anggota & Role</h3>
                                <div class="badge badge-neutral badge-lg">
                                    Total: {{ $allUsers->count() }} pengguna
                                </div>
                            </div>
                            
                            <div class="bg-base-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Centang pengguna untuk menambahkan mereka ke grup. Pilih role dan perbarui informasi pengguna.</span>
                                </div>
                            </div>

                            {{-- Search Box --}}
                            <div class="mb-4">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="user-search"
                                        placeholder="Search users by name or email..."
                                        class="input input-bordered w-full pl-10"
                                    />
                                    <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            @php
                                $currentMembers = $allUsers->filter(fn($u) => in_array($u->id, $currentMemberIds));
                                $incomingUsers = collect(); // For validation errors
                                $availableUsers = $allUsers->reject(fn($u) => in_array($u->id, $currentMemberIds));
                                $currentUserId = auth()->id();
                            @endphp

                            {{-- Section 1: Current Members (Read-only with Remove Button) --}}
                            @if($currentMembers->isNotEmpty())
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            Anggota Saat Ini
                                        </h4>
                                        <span class="badge badge-info badge-lg">{{ $currentMembers->count() }} anggota</span>
                                    </div>
                                    
                                    <div id="current-members-list" class="space-y-3 max-h-[500px] overflow-y-auto border-2 border-info rounded-lg p-4 bg-info/5">
                                        @foreach($currentMembers as $user)
                                            @php
                                                $userMembership = $group->groupMembers()->where('user_id', $user->id)->first();
                                                $currentRole = $userMembership ? $userMembership->role : null;
                                                $isCurrentUser = $user->id === $currentUserId;
                                            @endphp
                                            <div class="member-card border-2 border-info bg-base-100 rounded-lg p-4 transition-all" data-user-id="{{ $user->id }}">
                                                <div class="flex flex-col lg:flex-row items-start gap-4">
                                                    {{-- User Info --}}
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <div class="font-semibold text-base text-gray-900">{{ $user->name }}</div>
                                                            @if($currentRole)
                                                                <x-mary-badge 
                                                                    value="{{ $currentRole->display_name }}" 
                                                                    class="badge-info badge-sm"
                                                                />
                                                            @endif
                                                        </div>
                                                        <div class="text-sm text-gray-500 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                            </svg>
                                                            {{ $user->email }}
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Remove Button or Self Badge --}}
                                                    <div class="flex items-center gap-2">
                                                        @if(!$isCurrentUser)
                                                            <button 
                                                                type="button"
                                                                onclick="removeMember({{ $user->id }})"
                                                                class="btn btn-error btn-xs remove-member-btn"
                                                            >
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                                Remove
                                                            </button>
                                                            <button 
                                                                type="button"
                                                                onclick="undoRemove({{ $user->id }})"
                                                                class="btn btn-success btn-xs undo-remove-btn"
                                                                style="display: none;"
                                                            >
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                                </svg>
                                                                Undo
                                                            </button>
                                                        @else
                                                            <x-mary-badge 
                                                                value="Cannot remove yourself" 
                                                                class="badge-warning badge-xs"
                                                            />
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- Hidden checkbox for form submission --}}
                                                <input 
                                                    type="checkbox"
                                                    name="users[]" 
                                                    value="{{ $user->id }}" 
                                                    checked
                                                    class="member-checkbox"
                                                    data-user-id="{{ $user->id }}"
                                                    style="display:none;"
                                                />
                                                {{-- Hidden role input --}}
                                                @if($currentRole)
                                                    <input type="hidden" name="user_roles[{{ $user->id }}]" value="{{ $currentRole->id }}">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Section 2: Incoming Users (Users being added - shown on validation errors) --}}
                            @if($incomingUsers->isNotEmpty())
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            Pengguna Masuk
                                        </h4>
                                        <span class="badge badge-success badge-lg">{{ $incomingUsers->count() }} pengguna</span>
                                    </div>
                                    
                                    <div class="space-y-3 max-h-[500px] overflow-y-auto border-2 border-success rounded-lg p-4 bg-success/5">
                                        @foreach($incomingUsers as $user)
                                            <div class="member-row border-2 border-success bg-base-100 rounded-lg p-4 hover:shadow-lg transition-all">
                                                <div class="flex flex-col lg:flex-row items-start gap-4">
                                                    <div class="pt-1">
                                                        <input 
                                                            type="checkbox"
                                                            name="users[]" 
                                                            value="{{ $user->id }}" 
                                                            checked
                                                            class="checkbox checkbox-success member-checkbox"
                                                            data-user-id="{{ $user->id }}"
                                                            id="user_{{ $user->id }}"
                                                        />
                                                    </div>
                                                    
                                                    <div class="flex-1">
                                                        <div class="font-semibold text-base text-gray-900">{{ $user->name }}</div>
                                                        <div class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                            </svg>
                                                            {{ $user->email }}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="role-selector min-w-full lg:min-w-[220px]">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                                                        <select 
                                                            name="user_roles[{{ $user->id }}]" 
                                                            class="select select-bordered select-sm w-full"
                                                        >
                                                            @foreach($groupRoles as $role)
                                                                <option value="{{ $role->id }}" {{ $loop->first ? 'selected' : '' }}>
                                                                    {{ $role->display_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Section 3: Available Users (Not in group) --}}
                            @if($availableUsers->isNotEmpty())
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            Pengguna Tersedia
                                        </h4>
                                        <span class="badge badge-ghost badge-lg">{{ $availableUsers->count() }} tersedia</span>
                                    </div>
                                    
                                    <div id="available-members-list" class="space-y-3 max-h-[500px] overflow-y-auto border-2 border-base-300 rounded-lg p-4 bg-base-100">
                                        @foreach($availableUsers as $user)
                                            <div class="member-row border-2 border-base-300 rounded-lg p-4 hover:border-primary transition-colors bg-base-100">
                                                <div class="flex flex-col lg:flex-row items-start gap-4">
                                                    {{-- Checkbox --}}
                                                    <div class="pt-1">
                                                        <input 
                                                            type="checkbox"
                                                            name="users[]" 
                                                            value="{{ $user->id }}" 
                                                            class="checkbox checkbox-primary member-checkbox"
                                                            data-user-id="{{ $user->id }}"
                                                            id="user_{{ $user->id }}"
                                                        />
                                                    </div>
                                                    
                                                    {{-- User Info --}}
                                                    <div class="flex-1">
                                                        <div class="font-semibold text-base text-gray-900">{{ $user->name }}</div>
                                                        <div class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                            </svg>
                                                            {{ $user->email }}
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Role Selector (Hidden by default) --}}
                                                    <div class="role-selector min-w-full lg:min-w-[220px]" style="display: none;">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                                                        <select 
                                                            name="user_roles[{{ $user->id }}]" 
                                                            class="select select-bordered select-sm w-full"
                                                            disabled
                                                        >
                                                            @foreach($groupRoles as $role)
                                                                <option value="{{ $role->id }}" {{ $loop->first ? 'selected' : '' }}>
                                                                    {{ $role->display_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span>Select to assign role</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-6 border-t">
                            <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-ghost">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Perbarui Anggota
                            </button>
                        </div>
                    </div>
                </form>
            </x-mary-card>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/admin/group-member-selector.js'])
        
        <script>
            /**
             * Remove a member from the current members list
             * Highlights the card in red and unchecks the hidden checkbox
             */
            function removeMember(userId) {
                const card = document.querySelector(`.member-card[data-user-id="${userId}"]`);
                if (!card) return;
                
                // Add red highlight styling
                card.classList.remove('border-info', 'bg-base-100');
                card.classList.add('border-error', 'bg-error/10');
                
                // Uncheck the hidden checkbox so user is removed on form submit
                const checkbox = card.querySelector('.member-checkbox');
                if (checkbox) {
                    checkbox.checked = false;
                }
                
                // Toggle button visibility
                const removeBtn = card.querySelector('.remove-member-btn');
                const undoBtn = card.querySelector('.undo-remove-btn');
                if (removeBtn) removeBtn.style.display = 'none';
                if (undoBtn) undoBtn.style.display = 'inline-flex';
            }
            
            /**
             * Undo removal - restore original styling
             */
            function undoRemove(userId) {
                const card = document.querySelector(`.member-card[data-user-id="${userId}"]`);
                if (!card) return;
                
                // Restore original styling
                card.classList.remove('border-error', 'bg-error/10');
                card.classList.add('border-info', 'bg-base-100');
                
                // Re-check the hidden checkbox
                const checkbox = card.querySelector('.member-checkbox');
                if (checkbox) {
                    checkbox.checked = true;
                }
                
                // Toggle button visibility
                const removeBtn = card.querySelector('.remove-member-btn');
                const undoBtn = card.querySelector('.undo-remove-btn');
                if (removeBtn) removeBtn.style.display = 'inline-flex';
                if (undoBtn) undoBtn.style.display = 'none';
            }
        </script>
    @endpush
</x-admin.layout>
