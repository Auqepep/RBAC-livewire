<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users Management') }}
            </h2>
            <x-mary-button icon="o-plus" class="btn-primary" link="{{ route('admin.users.create') }}">
                Create User
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

                @if($users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/4">User</th>
                                    <th class="w-1/4">Email</th>
                                    <th class="w-1/4">Groups</th>
                                    <th class="w-1/6 text-center">Status</th>
                                    <th class="w-1/6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">Joined {{ $user->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ $user->email }}</div>
                                        </td>
                                        <td>
                                            @if($user->groupMembers->count() > 0)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($user->groupMembers->take(3) as $membership)
                                                        <x-mary-badge 
                                                            value="{{ $membership->group->name }}" 
                                                            class="badge-primary badge-sm" 
                                                        />
                                                    @endforeach
                                                    @if($user->groupMembers->count() > 3)
                                                        <x-mary-badge 
                                                            value="+{{ $user->groupMembers->count() - 3 }} more" 
                                                            class="badge-neutral badge-sm" 
                                                        />
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">No groups</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($user->email_verified_at)
                                                <x-mary-badge value="Verified" class="badge-success" />
                                            @else
                                                <x-mary-badge value="Unverified" class="badge-error" />
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center space-x-2">
                                                <x-mary-button icon="o-eye" class="btn-sm btn-ghost" link="{{ route('admin.users.show', $user) }}" />
                                                <x-mary-button icon="o-pencil" class="btn-sm btn-primary" link="{{ route('admin.users.edit', $user) }}" />
                                                
                                                @if($user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
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
                        {{ $users->links() }}
                    </div>
                @else
                    <x-mary-alert icon="o-information-circle" class="alert-info">
                        No users found. <a href="{{ route('admin.users.create') }}" class="link link-primary">Create the first user</a>.
                    </x-mary-alert>
                @endif
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
