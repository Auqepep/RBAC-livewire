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

                @if($groups->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/4">Group Name</th>
                                    <th class="w-1/3">Description</th>
                                    <th class="w-1/6 text-center">Members</th>
                                    <th class="w-1/6 text-center">Status</th>
                                    <th class="w-1/6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groups as $group)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-gray-900">{{ $group->name }}</div>
                                            <div class="text-sm text-gray-500">Created {{ $group->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ Str::limit($group->description ?? 'No description', 100) }}</div>
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
