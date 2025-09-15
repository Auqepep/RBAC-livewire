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
                                                {{ $member->pivot && $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('M d, Y H:i') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                @if($member->pivot && $member->pivot->added_by)
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
    </div>
</x-admin.layout>
