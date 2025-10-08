<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters and Search -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Status Filter -->
                <div>
                    <label for="filter" class="block text-sm font-medium text-gray-700 mb-2">Status Filter</label>
                    <select wire:model.live="filter" id="filter" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="all">All Requests</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input wire:model.live="search" id="search" type="text" 
                               placeholder="Search by user or group name..." 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        @if($requests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                            <tr wire:key="request-{{ $request->id }}" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">
                                                {{ substr($request->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->group->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->group->members->count() }} members</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($request->message)
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            {{ Str::limit($request->message, 100) }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 italic">No message</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->status === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $request->created_at->format('M d, Y') }}</div>
                                    <div>{{ $request->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($request->status === 'pending')
                                        <div class="flex space-x-2">
                                            <button wire:click="quickApprove({{ $request->id }})" 
                                                    class="text-green-600 hover:text-green-900 transition-colors">
                                                Approve
                                            </button>
                                            <button wire:click="quickReject({{ $request->id }})" 
                                                    class="text-red-600 hover:text-red-900 transition-colors">
                                                Reject
                                            </button>
                                            <button wire:click="openResponseModal({{ $request->id }}, 'approve')" 
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                                Respond
                                            </button>
                                        </div>
                                    @else
                                        <div class="text-gray-400">
                                            Reviewed by {{ $request->reviewer->name ?? 'Unknown' }}
                                            @if($request->reviewed_at)
                                                <div class="text-xs">{{ $request->reviewed_at->diffForHumans() }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Requests Found</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if($search || $filter !== 'all')
                        No requests match your current filters.
                    @else
                        No group join requests have been submitted yet.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Response Modal -->
    @if($showResponseModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ ucfirst($responseAction) }} Request
                    </h3>
                    
                    <div class="mb-4">
                        <label for="adminMessage" class="block text-sm font-medium text-gray-700 mb-2">
                            Response Message (Optional)
                        </label>
                        <textarea wire:model="adminMessage" id="adminMessage" rows="3"
                                  placeholder="Add a message for the user about your decision..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('adminMessage') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button wire:click="closeResponseModal" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="processResponse" 
                                class="px-4 py-2 {{ $responseAction === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white rounded-md transition-colors">
                            {{ ucfirst($responseAction) }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
