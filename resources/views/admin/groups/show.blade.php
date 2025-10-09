<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Group Details: {{ $group->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.groups.edit', $group) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Group
                </a>
                <a href="{{ route('admin.groups.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Groups
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Group Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Group Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $group->name }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1">
                            @if($group->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created By</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $group->creator?->name ?? 'System' }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created At</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $group->created_at->format('M d, Y H:i:s') }}</div>
                    </div>
                    
                    @if($group->description)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $group->description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Group Members -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Group Members ({{ $group->members->count() }})
                </h3>
                
                @if($group->members->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Joined At
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Added By
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($group->members as $member)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $member->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                {{ $member->email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                {{ $member->pivot?->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('M d, Y H:i') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                @if($member->pivot?->added_by)
                                                    @php
                                                        $addedBy = \App\Models\User::find($member->pivot->added_by);
                                                    @endphp
                                                    {{ $addedBy?->name ?? 'Unknown' }}
                                                @else
                                                    System
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">This group has no members yet.</p>
                        <a href="{{ route('admin.groups.edit', $group) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Members
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Role Management for Group -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Role Management for {{ $group->name }}
                    </h3>
                    <div class="space-x-2">
                        <a href="{{ route('admin.groups.roles.create', $group) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Create Role for This Group
                        </a>
                        <a href="{{ route('admin.groups.roles.index', $group) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Manage Group Roles
                        </a>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">
                                Group-Centric Role Management
                            </h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>
                                    Roles are now managed within the context of groups. Each user in this group can be assigned one role. 
                                    You can create group-specific roles or use existing generic roles (Manager, Staff, etc.).
                                </p>
                                <ul class="mt-2 list-disc list-inside">
                                    <li>Create roles specific to this group's needs</li>
                                    <li>Assign roles to group members</li>
                                    <li>Manage role permissions within this group context</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>
