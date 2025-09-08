<x-user.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Groups') }}
            </h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- User Info Card -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 bg-gray-300 rounded-full flex items-center justify-center">
                        <span class="text-xl font-medium text-gray-700">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" style="{{ $role->badge_color }}">
                                    {{ $role->display_name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Groups Section -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">
                    Groups You Belong To ({{ $groups->count() }})
                </h3>

                @if($groups->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($groups as $group)
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            {{ $group->name }}
                                        </h4>
                                        @if($group->description)
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $group->description }}
                                            </p>
                                        @endif
                                    </div>
                                    @if($group->is_active)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </div>

                                <!-- Group Members -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Members</span>
                                        <span class="text-sm text-gray-500">{{ $group->members->count() }}</span>
                                    </div>
                                    
                                    @if($group->members->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($group->members->take(5) as $member)
                                                <div class="flex items-center space-x-2 bg-white rounded-full px-3 py-1 border border-gray-200">
                                                    <div class="h-6 w-6 bg-gray-300 rounded-full flex items-center justify-center">
                                                        <span class="text-xs font-medium text-gray-700">
                                                            {{ substr($member->user->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-700">
                                                        {{ $member->user->name }}
                                                    </span>
                                                </div>
                                            @endforeach
                                            
                                            @if($group->members->count() > 5)
                                                <div class="flex items-center justify-center h-8 w-8 bg-gray-200 rounded-full">
                                                    <span class="text-xs text-gray-600">
                                                        +{{ $group->members->count() - 5 }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 italic">No other members</p>
                                    @endif
                                </div>

                                <!-- Group Stats -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Created</span>
                                            <p class="font-medium text-gray-900">
                                                {{ $group->created_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Your Role</span>
                                            <p class="font-medium text-gray-900">
                                                Member
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto h-24 w-24 text-gray-400">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No Groups Yet</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            You haven't been added to any groups yet. Contact your administrator to join groups.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-user.layout>
