<x-admin.layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight truncate">
                {{ __('Edit Group') }}: {{ Str::limit($group->name, 30) }}
            </h2>
            <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary btn-sm self-start sm:self-auto">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="hidden sm:inline">{{ __('Back to Groups') }}</span>
                <span class="sm:hidden">{{ __('Back') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            @if (session('success'))
                <x-mary-alert icon="o-check-circle" class="alert-success" dismissible>
                    {{ session('success') }}
                </x-mary-alert>
            @endif

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

                <form method="POST" action="{{ route('admin.groups.update', $group) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Group Basic Info -->
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Group Name') }} <span class="text-red-500">*</span></label>
                                <input 
                                    type="text"
                                    name="name" 
                                    value="{{ old('name', $group->name) }}"
                                    placeholder="{{ __('Enter group name') }}"
                                    required
                                    class="input input-bordered w-full @error('name') input-error @enderror"
                                />
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-sm text-gray-500 mt-1">{{ __('This name must be unique') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                                <textarea 
                                    name="description" 
                                    rows="3"
                                    placeholder="{{ __('Describe the purpose of this group') }}"
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
                                    {{ __('Active') }}
                                    <span class="block text-xs text-gray-500 font-normal">{{ __('Inactive groups cannot be assigned to users') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- OAuth / Third-Party App Integration -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                {{ __('Gateway & Third-Party App Integration') }}
                            </h3>
                            
                            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-2 text-sm text-blue-800">
                                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-semibold">{{ __('Configure OAuth 2.0 Integration') }}</p>
                                        <p class="text-xs mt-1">{{ __('When enabled, users accessing the gateway will be redirected to your third-party application with authentication details.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <input 
                                        type="checkbox"
                                        name="enable_gateway_redirect" 
                                        value="1" 
                                        {{ old('enable_gateway_redirect', $group->enable_gateway_redirect) ? 'checked' : '' }}
                                        class="checkbox checkbox-primary"
                                        id="enable_gateway_redirect"
                                    />
                                    <label for="enable_gateway_redirect" class="text-sm font-medium text-gray-700 cursor-pointer">
                                        {{ __('Enable Gateway Redirect') }}
                                        <span class="block text-xs text-gray-500 font-normal">{{ __('Automatically redirect authenticated users to third-party app') }}</span>
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Third-Party App URL') }}
                                        <span class="text-xs text-gray-500 ml-1">({{ __('Optional') }})</span>
                                    </label>
                                    <input 
                                        type="url"
                                        name="third_party_app_url" 
                                        value="{{ old('third_party_app_url', $group->third_party_app_url) }}"
                                        placeholder="https://your-app.com/oauth/callback"
                                        class="input input-bordered w-full @error('third_party_app_url') input-error @enderror"
                                    />
                                    @error('third_party_app_url')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-sm text-gray-500 mt-1">{{ __('URL where users will be redirected after gateway authentication') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('OAuth Client ID') }}
                                        <span class="text-xs text-gray-500 ml-1">({{ __('Optional') }})</span>
                                    </label>
                                    <input 
                                        type="text"
                                        name="oauth_client_id" 
                                        value="{{ old('oauth_client_id', $group->oauth_client_id) }}"
                                        placeholder="9d425c82-aa6c-4a1b-9c3d-1234567890ab"
                                        class="input input-bordered w-full font-mono text-sm @error('oauth_client_id') input-error @enderror"
                                    />
                                    @error('oauth_client_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-sm text-gray-500 mt-1">{{ __('OAuth 2.0 Client ID for the third-party application') }}</p>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-3 text-xs text-gray-600">
                                    <p class="font-semibold mb-1"> {{ __('What gets sent to the third-party app:') }}</p>
                                    <ul class="list-disc list-inside space-y-0.5 ml-2">
                                        <li>{{ __('User ID, Email, Name') }}</li>
                                        <li>{{ __('Group ID & Name') }}</li>
                                        <li>{{ __('User\'s Role in this group') }}</li>
                                        <li>{{ __('OAuth Client ID') }} ({{ __('Optional') }})</li>
                                        <li>{{ __('Timestamp') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Group Members Section -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Group Members') }}</h3>
                            
                            <div class="bg-base-200 rounded-lg p-3 sm:p-4 mb-4">
                                <div class="flex items-start sm:items-center gap-2 text-xs sm:text-sm text-gray-600">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Pilih pengguna dan tetapkan role mereka. Klik tombol hapus untuk mengeluarkan dari grup.</span>
                                </div>
                            </div>

                            {{-- Search Box --}}
                            <div class="mb-4">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="user-search"
                                        placeholder="Cari pengguna..."
                                        class="input input-bordered input-sm sm:input-md w-full pl-8 sm:pl-10 text-sm"
                                    />
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 absolute left-2 sm:left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            @php
                                // Current members - users already in the group
                                $currentMembers = $users->filter(fn($u) => in_array($u->id, $groupUsers));
                                
                                // Incoming users - users checked but not yet in group (for validation errors)
                                $incomingUsers = collect();
                                if (old('users')) {
                                    $incomingUsers = $users->filter(fn($u) => 
                                        in_array($u->id, old('users', [])) && !in_array($u->id, $groupUsers)
                                    );
                                }
                                
                                // Users marked for removal
                                $removedUsers = collect();
                                if (old('users')) {
                                    $removedUsers = $currentMembers->reject(fn($u) => 
                                        in_array($u->id, old('users', []))
                                    );
                                }
                                
                                // Available users - not in group and not checked
                                $availableUsers = $users->reject(fn($u) => 
                                    in_array($u->id, $groupUsers) || in_array($u->id, old('users', []))
                                );
                            @endphp

                            {{-- Section 1: Current Members (With Remove Button) --}}
                            @if($currentMembers->isNotEmpty())
                                <div class="mb-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                        <h4 class="text-sm sm:text-md font-semibold text-gray-800 flex items-center gap-2">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            {{ __('Current Members') }}
                                        </h4>
                                        <span class="badge badge-info badge-xs sm:badge-sm md:badge-md">{{ $currentMembers->count() }} {{ __('member(s)') }}</span>
                                    </div>
                                    
                                    <div class="space-y-3 max-h-[300px] overflow-y-auto border-2 border-info rounded-lg p-4 bg-info/5">
                                        @foreach($currentMembers as $user)
                                            @php
                                                $userMembership = $group->groupMembers()->where('user_id', $user->id)->first();
                                                $currentRole = $userMembership ? $userMembership->role : null;
                                                $isCurrentUser = $user->id === Auth::id();
                                            @endphp
                                            <div class="member-card border-2 border-info bg-base-100 rounded-lg p-3 sm:p-4 transition-all" data-user-id="{{ $user->id }}">
                                                <div class="flex flex-col gap-3">
                                                    <div class="flex items-center justify-between gap-3">
                                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                                            {{-- Member Icon --}}
                                                            <div class="w-10 h-10 bg-info/20 rounded-full flex items-center justify-center text-info font-semibold shrink-0">
                                                                {{ substr($user->name, 0, 1) }}
                                                            </div>
                                                            
                                                            {{-- User Info --}}
                                                            <div class="flex-1 min-w-0">
                                                                <div class="font-semibold text-sm sm:text-base text-gray-900 truncate">
                                                                    {{ $user->name }}
                                                                    @if($isCurrentUser)
                                                                        <span class="text-blue-600 text-xs sm:text-sm font-normal">(You)</span>
                                                                    @endif
                                                                </div>
                                                                <div class="text-xs sm:text-sm text-gray-500 truncate">{{ $user->email }}</div>
                                                            </div>
                                                        </div>
                                                        
                                                        {{-- Role Badge & Remove Button --}}
                                                        <div class="flex items-center gap-2 shrink-0">
                                                            @if($currentRole)
                                                                <x-mary-badge value="{{ $currentRole->display_name }}" class="badge-info" />
                                                            @endif
                                                            @if(!$isCurrentUser)
                                                                <button 
                                                                    type="button"
                                                                    class="btn btn-error btn-xs remove-member-btn"
                                                                    data-user-id="{{ $user->id }}"
                                                                    onclick="removeMember({{ $user->id }})"
                                                                >
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            @else
                                                                <x-mary-badge value="Tidak dapat menghapus diri sendiri" class="badge-warning badge-xs" />
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Hidden checkbox to keep them in form (will be unchecked by JS when removed) --}}
                                                <input type="checkbox" name="users[]" value="{{ $user->id }}" checked style="display:none" class="member-checkbox" data-user-id="{{ $user->id }}">
                                                <input type="hidden" name="user_roles[{{ $user->id }}]" value="{{ $currentRole?->id }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Section 2: Incoming Users (To be added) --}}
                            @if($incomingUsers->isNotEmpty())
                                <div class="mb-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                        <h4 class="text-sm sm:text-md font-semibold text-gray-800 flex items-center gap-2">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            Incoming Users
                                        </h4>
                                        <span class="badge badge-success badge-xs sm:badge-sm md:badge-md">{{ $incomingUsers->count() }} to add</span>
                                    </div>
                                    
                                    <div id="current-members-list" class="space-y-3 max-h-[300px] overflow-y-auto border-2 border-success rounded-lg p-4 bg-success/5">
                                        @foreach($incomingUsers as $user)
                                            <div class="member-row border-2 border-success bg-base-100 rounded-lg p-3 sm:p-4 hover:shadow-lg transition-all">
                                                <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">
                                                    <div class="flex items-start gap-3 sm:gap-4 flex-1">
                                                        {{-- Checkbox --}}
                                                        <div class="pt-1 shrink-0">
                                                            <input 
                                                                type="checkbox"
                                                                name="users[]" 
                                                                value="{{ $user->id }}" 
                                                                checked
                                                                class="checkbox checkbox-success checkbox-sm sm:checkbox-md member-checkbox"
                                                                data-user-id="{{ $user->id }}"
                                                                id="user_{{ $user->id }}"
                                                            />
                                                        </div>
                                                        
                                                        {{-- User Info --}}
                                                        <label for="user_{{ $user->id }}" class="flex-1 cursor-pointer min-w-0">
                                                            <div class="font-semibold text-sm sm:text-base text-gray-900 truncate">{{ $user->name }}</div>
                                                            <div class="text-xs sm:text-sm text-gray-500 flex items-center gap-2 mt-1">
                                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                                </svg>
                                                                <span class="truncate">{{ $user->email }}</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    
                                                    {{-- Role Selector --}}
                                                    <div class="role-selector w-full sm:w-auto sm:min-w-[180px] md:min-w-[220px]">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                                                        <select 
                                                            name="user_roles[{{ $user->id }}]" 
                                                            class="select select-bordered select-xs sm:select-sm w-full"
                                                        >
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->id }}" {{ $loop->first ? 'selected' : '' }}>
                                                                    {{ $role->display_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span>Will be added with this role</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Section 3: Pengguna Tersedia (Tidak dalam grup) --}}
                            @if($availableUsers->isNotEmpty())
                                <div>
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                        <h4 class="text-sm sm:text-md font-semibold text-gray-800 flex items-center gap-2">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            Pengguna Tersedia
                                        </h4>
                                        <span class="badge badge-ghost badge-xs sm:badge-sm md:badge-md">{{ $availableUsers->count() }} tersedia</span>
                                    </div>
                                    
                                    <div id="available-members-list" class="space-y-3 max-h-[300px] overflow-y-auto border-2 border-base-300 rounded-lg p-4 bg-base-100">
                                        @foreach($availableUsers as $user)
                                            <div class="member-row border-2 border-base-300 rounded-lg p-3 sm:p-4 hover:border-primary transition-colors bg-base-100">
                                                <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">
                                                    <div class="flex items-start gap-3 sm:gap-4 flex-1">
                                                        {{-- Checkbox --}}
                                                        <div class="pt-1 shrink-0">
                                                            <input 
                                                                type="checkbox"
                                                                name="users[]" 
                                                                value="{{ $user->id }}" 
                                                                class="checkbox checkbox-primary checkbox-sm sm:checkbox-md member-checkbox"
                                                                data-user-id="{{ $user->id }}"
                                                                id="user_{{ $user->id }}"
                                                            />
                                                        </div>
                                                        
                                                        {{-- User Info --}}
                                                        <label for="user_{{ $user->id }}" class="flex-1 cursor-pointer min-w-0">
                                                            <div class="font-semibold text-sm sm:text-base text-gray-900 truncate">
                                                                {{ $user->name }}
                                                                @if($user->id === Auth::id())
                                                                    <span class="text-blue-600 text-xs sm:text-sm font-normal">({{ __('You') }})</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-xs sm:text-sm text-gray-500 flex items-center gap-2 mt-1">
                                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                                </svg>
                                                                <span class="truncate">{{ $user->email }}</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    
                                                    {{-- Role Selector (Hidden by default) --}}
                                                    <div class="role-selector w-full sm:w-auto sm:min-w-[180px] md:min-w-[220px]" style="display: none;">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                                                        <select 
                                                            name="user_roles[{{ $user->id }}]" 
                                                            class="select select-bordered select-xs sm:select-sm w-full"
                                                            disabled
                                                        >
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->id }}" {{ $loop->first ? 'selected' : '' }}>
                                                                    {{ $role->display_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span class="truncate">Select to assign role</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($users->isEmpty())
                                <div class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No users available to add</p>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 pt-4 sm:pt-6 border-t">
                            <a href="{{ route('admin.groups.index') }}" class="btn btn-ghost btn-sm sm:btn-md w-full sm:w-auto">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-warning btn-sm sm:btn-md w-full sm:w-auto">
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

    @push('scripts')
        @vite(['resources/js/admin/group-member-selector.js'])
        <script>
            function removeMember(userId) {
                const card = document.querySelector(`.member-card[data-user-id="${userId}"]`);
                const checkbox = card.querySelector(`.member-checkbox[data-user-id="${userId}"]`);
                
                if (card && checkbox) {
                    // Highlight card in red
                    card.classList.remove('border-info', 'bg-base-100');
                    card.classList.add('border-error', 'bg-error/10');
                    
                    // Uncheck the hidden checkbox
                    checkbox.checked = false;
                    
                    // Change button text
                    const btn = card.querySelector('.remove-member-btn');
                    btn.innerHTML = `
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Undo
                    `;
                    btn.classList.remove('btn-error');
                    btn.classList.add('btn-warning');
                    btn.onclick = () => undoRemove(userId);
                }
            }
            
            function undoRemove(userId) {
                const card = document.querySelector(`.member-card[data-user-id="${userId}"]`);
                const checkbox = card.querySelector(`.member-checkbox[data-user-id="${userId}"]`);
                
                if (card && checkbox) {
                    // Restore original styling
                    card.classList.remove('border-error', 'bg-error/10');
                    card.classList.add('border-info', 'bg-base-100');
                    
                    // Check the hidden checkbox
                    checkbox.checked = true;
                    
                    // Restore button
                    const btn = card.querySelector('.remove-member-btn');
                    btn.innerHTML = `
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Remove
                    `;
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-error');
                    btn.onclick = () => removeMember(userId);
                }
            }
        </script>
    @endpush
</x-admin.layout>
