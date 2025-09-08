<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <flux:heading size="lg">{{ __('Groups Management') }}</flux:heading>
            <flux:button href="{{ route('admin.groups.create') }}" variant="primary">
                <flux:icon.plus class="size-4" />
                Create New Group
            </flux:button>
        </div>
    </x-slot>

    <flux:card>
        <flux:table :paginate="$groups">
            <flux:columns>
                <flux:column>Group</flux:column>
                <flux:column>Members</flux:column>
                <flux:column>Status</flux:column>
                <flux:column>Created By</flux:column>
                <flux:column>Actions</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse ($groups as $group)
                    <flux:row>
                        <flux:cell>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $group->name }}
                                </div>
                                @if($group->description)
                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ Str::limit($group->description, 50) }}
                                    </div>
                                @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $group->users_count }} members
                                </div>
                                @if($group->users_count > 0)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $group->users->take(3)->pluck('name')->implode(', ') }}
                                        @if($group->users_count > 3)
                                            <span class="text-gray-400">+{{ $group->users_count - 3 }} more</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($group->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $group->creator?->name ?? 'System' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $group->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.groups.show', $group) }}" class="text-indigo-600 hover:text-indigo-900">
                                        View
                                    </a>
                                    <a href="{{ route('admin.groups.edit', $group) }}" class="text-blue-600 hover:text-blue-900">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this group?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No groups found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($groups->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $groups->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>
