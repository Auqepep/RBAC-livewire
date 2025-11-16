<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Group') }}: {{ $group->name }}
            </h2>
            <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary btn-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Groups
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <x-mary-alert icon="o-check-circle" class="alert-success" dismissible>
                    {{ session('success') }}
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
                    
                    <div class="space-y-6">
                        <!-- Group Basic Info -->
                        <div class="grid grid-cols-1 gap-6">
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
                                <p class="text-sm text-gray-500 mt-1">This name must be unique</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea 
                                    name="description" 
                                    rows="3"
                                    placeholder="Describe the purpose of this group"
                                    class="textarea textarea-bordered w-full @error('description') textarea-error @enderror"
                                >{{ old('description', $group->description) }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center gap-3">
                                <input 
                                    type="checkbox"
                                    name="is_active" 
                                    value="1" 
                                    {{ old('is_active', $group->is_active) ? 'checked' : '' }}
                                    class="checkbox checkbox-primary"
                                    id="is_active"
                                />
                                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                                    Active
                                    <span class="block text-xs text-gray-500 font-normal">Inactive groups cannot be assigned to users</span>
                                </label>
                            </div>
                        </div>

                        <!-- Group Members Section -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Group Members</h3>
                            <div id="group-members-list" class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
                                @foreach($users as $user)
                                    @php
                                        $isChecked = in_array($user->id, old('users', $groupUsers));
                                        $userMembership = $group->groupMembers()->where('user_id', $user->id)->first();
                                        $currentRole = $userMembership ? $userMembership->role : null;
                                    @endphp
                                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-colors member-row">
                                        <input 
                                            type="checkbox"
                                            name="users[]" 
                                            value="{{ $user->id }}" 
                                            {{ $isChecked ? 'checked' : '' }}
                                            class="checkbox checkbox-primary member-checkbox"
                                            data-user-id="{{ $user->id }}"
                                            id="user_{{ $user->id }}"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <label for="user_{{ $user->id }}" class="font-medium text-gray-900 cursor-pointer block truncate">
                                                {{ $user->name }}
                                            </label>
                                            <div class="text-sm text-gray-500 truncate">{{ $user->email }}</div>
                                        </div>
                                        <div class="role-selector flex-shrink-0" style="{{ !$isChecked ? 'display: none;' : '' }}">
                                            <select 
                                                name="user_roles[{{ $user->id }}]" 
                                                class="select select-sm select-bordered"
                                                {{ !$isChecked ? 'disabled' : '' }}
                                            >
                                                @foreach($group->roles as $role)
                                                    <option value="{{ $role->id }}" 
                                                        {{ (old("user_roles.{$user->id}", $currentRole?->id) == $role->id) ? 'selected' : '' }}>
                                                        {{ $role->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-sm text-gray-500 mt-3">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Select users and assign them roles. Unchecking a user will remove them from the group.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-6 border-t">
                            <a href="{{ route('admin.groups.index') }}" class="btn btn-ghost">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Group
                            </button>
                        </div>
                    </div>
                </form>
            </x-mary-card>
        </div>
    </div>

    @vite(['resources/js/admin/group-member-selector.js'])
</x-admin.layout>
