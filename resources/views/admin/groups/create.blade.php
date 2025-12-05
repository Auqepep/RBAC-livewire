<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Group') }}
            </h2>
            <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.groups.index') }}">
                {{ __('Back to Groups') }}
            </x-mary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-mary-card>
                @if($errors->any())
                    <x-mary-alert icon="o-x-circle" class="alert-error mb-6">
                        <strong>{{ __('Please fix the following errors:') }}</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-mary-alert>
                @endif

                <form method="POST" action="{{ route('admin.groups.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <x-mary-input 
                            label="{{ __('Group Name') }}" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            placeholder="{{ __('Enter group name') }}"
                            hint="{{ __('This name must be unique') }}"
                        />

                        <x-mary-textarea 
                            label="{{ __('Description') }}" 
                            name="description" 
                            value="{{ old('description') }}" 
                            placeholder="{{ __('Describe the purpose of this group') }}"
                            rows="3"
                        />

                        <x-mary-checkbox 
                            label="{{ __('Active') }}" 
                            name="is_active" 
                            value="1" 
                            checked="{{ old('is_active', true) }}"
                            hint="{{ __('Inactive groups cannot be assigned to users') }}"
                        />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <span class="text-lg">{{ __('Members') }} ({{ __('Optional') }})</span>
                            </label>
                            <div class="bg-base-200 rounded-lg p-4 mb-3">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ __('Select users and assign them roles. Unchecking a user will remove them from the group.') }}</span>
                                </div>
                            </div>

                            {{-- Search Box --}}
                            <div class="mb-3">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="user-search"
                                        placeholder="{{ __('Search users...') }}"
                                        class="input input-bordered w-full pl-10"
                                    />
                                    <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div id="group-members-list" class="space-y-3 max-h-[500px] overflow-y-auto border-2 border-base-300 rounded-lg p-4 bg-base-100">
                                @foreach($users as $user)
                                    @php
                                        $isChecked = in_array($user->id, old('users', []));
                                        $selectedRole = old('user_roles.' . $user->id, 'staff');
                                    @endphp
                                    <div class="member-row border-2 border-base-300 rounded-lg p-4 hover:border-primary transition-colors {{ $isChecked ? 'bg-primary/5 border-primary' : 'bg-base-100' }}">
                                        <div class="flex items-start gap-4">
                                            {{-- Checkbox --}}
                                            <div class="pt-1">
                                                <input type="checkbox" 
                                                       name="users[]" 
                                                       value="{{ $user->id }}" 
                                                       {{ $isChecked ? 'checked' : '' }}
                                                       class="checkbox checkbox-primary member-checkbox"
                                                       data-user-id="{{ $user->id }}"
                                                       id="user_{{ $user->id }}">
                                            </div>
                                            
                                            {{-- User Info --}}
                                            <label for="user_{{ $user->id }}" class="flex-1 cursor-pointer">
                                                <div class="font-semibold text-base text-gray-900">
                                                    {{ $user->name }}
                                                    @if($user->id === Auth::id())
                                                        <span class="text-blue-600 text-sm font-normal">({{ __('You') }})</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $user->email }}
                                                </div>
                                            </label>
                                            
                                            {{-- Role Selector --}}
                                            <div class="role-selector min-w-[200px]" style="{{ !$isChecked ? 'display: none;' : '' }}">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Assign Role') }}</label>
                                                <select name="user_roles[{{ $user->id }}]" class="select select-bordered select-sm w-full role-select" {{ !$isChecked ? 'disabled' : '' }}>
                                                    <option value="staff" {{ $selectedRole === 'staff' ? 'selected' : '' }}>
                                                        👤 {{ __('Staff') }}
                                                    </option>
                                                    <option value="manager" {{ $selectedRole === 'manager' ? 'selected' : '' }}>
                                                        👔 {{ __('Manager') }}
                                                    </option>
                                                </select>
                                                <div class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>{{ __('Role will be created for this group') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($users->isEmpty())
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <p>No users available to add</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4 border-t">
                            <x-mary-button 
                                label="{{ __('Cancel') }}" 
                                class="btn-secondary" 
                                link="{{ route('admin.groups.index') }}"
                            />
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('Create Group') }}
                            </button>
                        </div>
                    </div>
                </form>
            </x-mary-card>
        </div>
    </div>

    @vite(['resources/js/admin/group-member-selector.js'])
</x-admin.layout>
