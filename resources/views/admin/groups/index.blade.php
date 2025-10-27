<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Groups Management') }}
            </h2>
            <x-mary-button icon="o-plus" class="btn-primary" link="{{ route('admin.groups.create') }}">
                Create Group
            </x-mary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                <div class="mb-6 space-y-4">
                    <form method="GET" action="{{ route('admin.groups.index') }}" class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <x-mary-input 
                                name="search" 
                                value="{{ $search ?? '' }}" 
                                placeholder="Search groups by name or description..."
                                icon="o-magnifying-glass"
                            />
                        </div>
                        
                        <!-- Sort Controls -->
                        <div class="flex gap-2">
                            <x-mary-select 
                                name="sort_by" 
                                :options="[
                                    ['value' => 'name', 'label' => 'Name'],
                                    ['value' => 'created_at', 'label' => 'Date Created'],
                                    ['value' => 'group_members_count', 'label' => 'Member Count']
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortBy ?? 'name' }}"
                                placeholder="Sort by..."
                            />
                            
                            <x-mary-select 
                                name="sort_order" 
                                :options="[
                                    ['value' => 'asc', 'label' => 'A-Z / Oldest / Least'],
                                    ['value' => 'desc', 'label' => 'Z-A / Newest / Most']
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortOrder ?? 'asc' }}"
                                placeholder="Order..."
                            />
                        </div>
                        
                        <div class="flex gap-2">
                            <x-mary-button type="submit" class="btn-primary" icon="o-magnifying-glass">
                                Search
                            </x-mary-button>
                            @if(($search ?? '') || ($sortBy ?? 'name') !== 'name' || ($sortOrder ?? 'asc') !== 'asc')
                                <x-mary-button link="{{ route('admin.groups.index') }}" class="btn-secondary" icon="o-x-mark">
                                    Reset
                                </x-mary-button>
                            @endif
                        </div>
                    </form>
                </div>

                @if($groups->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/5">Group Name</th>
                                    <th class="w-1/3">Description</th>
                                    <th class="w-1/8 text-center">Members</th>
                                    <th class="w-1/8 text-center">Status</th>
                                    <th class="w-1/6">Created</th>
                                    <th class="w-1/6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groups as $group)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-gray-900">{{ $group->name }}</div>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ Str::limit($group->description ?? 'No description', 80) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <x-mary-badge value="{{ $group->group_members_count }}" class="badge-primary" />
                                        </td>
                                        <td class="text-center">
                                            @if($group->is_active)
                                                <x-mary-badge value="Active" class="badge-success" />
                                            @else
                                                <x-mary-badge value="Inactive" class="badge-error" />
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
                                                          onsubmit="return confirm('Are you sure you want to delete this group?')">
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

                    <div class="mt-4">
                        {{ $groups->links() }}
                    </div>
                @else
                    <x-mary-alert icon="o-information-circle" class="alert-info">
                        No groups found. <a href="{{ route('admin.groups.create') }}" class="link link-primary">Create the first group</a>.
                    </x-mary-alert>
                @endif
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
