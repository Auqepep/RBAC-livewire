<x-admin.layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                {{ __('Manage Groups') }}
            </h2>
            <x-mary-button icon="o-plus" class="btn-primary btn-sm sm:btn-md" link="{{ route('admin.groups.create') }}">
                <span class="hidden sm:inline">{{ __('Create Group') }}</span>
                <span class="sm:hidden">{{ __('Create') }}</span>
            </x-mary-button>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-mary-card>
                @if(session('success'))
                    <x-mary-alert icon="o-check-circle" class="alert-success mb-4">
                        {{ session('success') }}
                    </x-mary-alert>
                @endif

                @if(session('error'))
                    <x-mary-alert icon="o-x-circle" class="alert-error mb-4">
                        {{ session('error') }}
                    </x-mary-alert>
                @endif

                <!-- Search and Sort Form -->
                <div class="mb-4 sm:mb-6 space-y-4">
                    <form method="GET" action="{{ route('admin.groups.index') }}" class="flex flex-col gap-3 sm:gap-4">
                        <div class="flex-1">
                            <x-mary-input 
                                name="search" 
                                value="{{ $search ?? '' }}" 
                                placeholder="{{ __('Search') }} {{ strtolower(__('Groups')) }}..."
                                icon="o-magnifying-glass"
                            />
                        </div>
                        
                        <!-- Sort Controls -->
                        <div class="grid grid-cols-2 gap-2">
                            <x-mary-select 
                                name="sort_by" 
                                :options="[
                                    ['value' => 'name', 'label' => __('Name')],
                                    ['value' => 'created_at', 'label' => __('Date')],
                                    ['value' => 'group_members_count', 'label' => __('Members')]
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortBy ?? 'name' }}"
                                placeholder="{{ __('Sort by') }}..."
                            />
                            
                            <x-mary-select 
                                name="sort_order" 
                                :options="[
                                    ['value' => 'asc', 'label' => __('Ascending')],
                                    ['value' => 'desc', 'label' => __('Descending')]
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortOrder ?? 'asc' }}"
                                placeholder="{{ __('Sort') }}..."
                            />
                        </div>
                        
                        <div class="flex gap-2">
                            <x-mary-button type="submit" class="btn-primary flex-1 sm:flex-none" icon="o-magnifying-glass">
                                {{ __('Search') }}
                            </x-mary-button>
                            @if(($search ?? '') || ($sortBy ?? 'name') !== 'name' || ($sortOrder ?? 'asc') !== 'asc')
                                <x-mary-button link="{{ route('admin.groups.index') }}" class="btn-secondary flex-1 sm:flex-none" icon="o-x-mark">
                                    {{ __('Reset') }}
                                </x-mary-button>
                            @endif
                        </div>
                    </form>
                </div>

                @if($groups->count() > 0)
                    <!-- Desktop Table View (hidden on mobile) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/5">{{ __('Group Name') }}</th>
                                    <th class="w-1/3">{{ __('Description') }}</th>
                                    <th class="w-1/8 text-center">{{ __('Members') }}</th>
                                    <th class="w-1/8 text-center">{{ __('Status') }}</th>
                                    <th class="w-1/6">{{ __('Created') }}</th>
                                    <th class="w-1/6 text-center">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groups as $group)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-gray-900">{{ $group->name }}</div>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ Str::limit($group->description ?? __('No') . ' ' . strtolower(__('Description')), 80) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <x-mary-badge value="{{ $group->group_members_count }}" class="badge-primary badge-xs sm:badge-sm" />
                                        </td>
                                        <td class="text-center">
                                            @if($group->is_active)
                                                <x-mary-badge value="{{ __('Active') }}" class="badge-success badge-xs sm:badge-sm" />
                                            @else
                                                <x-mary-badge value="{{ __('Inactive') }}" class="badge-error badge-xs sm:badge-sm" />
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-sm text-gray-900">{{ $group->created_at->format('M j, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $group->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center space-x-2">
                                                <x-mary-button icon="o-eye" class="btn-sm btn-ghost" link="{{ route('admin.groups.show', $group) }}" />
                                                <x-mary-button icon="o-pencil" class="btn-sm btn-primary" link="{{ route('admin.groups.edit', $group) }}" />
                                                
                                                @if($group->group_members_count == 0)
                                                    <form method="POST" action="{{ route('admin.groups.destroy', $group) }}" class="inline" 
                                                          onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-mary-button icon="o-trash" class="btn-sm btn-error" type="submit" />
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View (shown on mobile only) -->
                    <div class="md:hidden space-y-4">
                        @foreach($groups as $group)
                            <div class="card bg-base-100 shadow-sm border">
                                <div class="card-body p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $group->name }}</h3>
                                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($group->description ?? __('No') . ' ' . strtolower(__('Description')), 60) }}</p>
                                        </div>
                                        @if($group->is_active)
                                            <x-mary-badge value="{{ __('Active') }}" class="badge-success badge-xs" />
                                        @else
                                            <x-mary-badge value="{{ __('Inactive') }}" class="badge-error badge-xs" />
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t">
                                        <div class="flex items-center gap-3 text-sm text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                                {{ $group->group_members_count }}
                                            </span>
                                            <span>{{ $group->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex gap-1">
                                            <x-mary-button icon="o-eye" class="btn-xs btn-ghost" link="{{ route('admin.groups.show', $group) }}" />
                                            <x-mary-button icon="o-pencil" class="btn-xs btn-primary" link="{{ route('admin.groups.edit', $group) }}" />
                                            @if($group->group_members_count == 0)
                                                <form method="POST" action="{{ route('admin.groups.destroy', $group) }}" class="inline" 
                                                      onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-mary-button icon="o-trash" class="btn-xs btn-error" type="submit" />
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $groups->links() }}
                    </div>
                @else
                    <x-mary-alert icon="o-information-circle" class="alert-info">
                        {{ __('No results found') }}. <a href="{{ route('admin.groups.create') }}" class="link link-primary">{{ __('Create') }} grup pertama</a>.
                    </x-mary-alert>
                @endif
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
