<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🔐 OAuth Client Apps
            </h2>
            <button onclick="document.getElementById('create_client_modal').showModal()" class="btn btn-primary btn-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Client Baru
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <x-mary-alert icon="o-check-circle" class="alert-success mb-4" dismissible>
                    {{ session('success') }}
                </x-mary-alert>
                
                @if(session('client_id') && session('new_secret'))
                    <x-mary-alert icon="o-key" class="alert-warning mb-4">
                        <div class="font-mono text-sm space-y-2">
                            <div><strong>Client ID:</strong> {{ session('client_id') }}</div>
                            <div><strong>New Secret:</strong> <span class="text-red-600">{{ session('new_secret') }}</span></div>
                            <div class="text-xs mt-2">⚠️ Save this secret now. You won't be able to see it again!</div>
                        </div>
                    </x-mary-alert>
                @endif
            @endif

            <x-mary-card>
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Nama Aplikasi</th>
                                <th>Client ID</th>
                                <th>Redirect URI</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $client)
                                <tr>
                                    <td class="font-semibold">{{ $client->name }}</td>
                                    <td>
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ Str::limit($client->id, 20) }}</code>
                                        <button onclick="navigator.clipboard.writeText('{{ $client->id }}')" class="btn btn-ghost btn-xs ml-1" title="Copy">
                                            📋
                                        </button>
                                    </td>
                                    <td>
                                        <div class="space-y-1">
                                            @foreach($client->redirect_uris as $uri)
                                                <div class="text-xs text-gray-600">• {{ Str::limit($uri, 40) }}</div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @if($client->revoked)
                                            <span class="badge badge-error badge-sm">Revoked</span>
                                        @else
                                            <span class="badge badge-success badge-sm">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        {{ $client->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button onclick="viewClient{{ $client->id }}Modal.showModal()" class="btn btn-info btn-xs">
                                                👁️ Lihat
                                            </button>
                                            <button onclick="editClient{{ $client->id }}Modal.showModal()" class="btn btn-warning btn-xs">
                                                ✏️ Edit
                                            </button>
                                            @if(!$client->revoked)
                                                <form method="POST" action="{{ route('admin.oauth-clients.destroy', $client) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-error btn-xs" onclick="return confirm('Cabut akses client ini? Semua token akan dinonaktifkan.')">
                                                        🚫 Cabut
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- View Modal -->
                                        <dialog id="viewClient{{ $client->id }}Modal" class="modal">
                                            <div class="modal-box max-w-2xl">
                                                <h3 class="font-bold text-lg mb-4">{{ $client->name }}</h3>
                                                
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="label font-semibold">Client ID</label>
                                                        <div class="flex gap-2">
                                                            <code class="flex-1 bg-gray-100 p-2 rounded text-sm break-all">{{ $client->id }}</code>
                                                            <button onclick="navigator.clipboard.writeText('{{ $client->id }}')" class="btn btn-sm">Copy</button>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="label font-semibold">Redirect URIs</label>
                                                        <ul class="list-disc list-inside space-y-1">
                                                            @foreach($client->redirect_uris as $uri)
                                                                <li class="text-sm">{{ $uri }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>

                                                    <div>
                                                        <label class="label font-semibold">Grant Types</label>
                                                        <div class="flex gap-2">
                                                            @foreach($client->grant_types as $type)
                                                                <span class="badge badge-sm">{{ $type }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                                                        <p class="text-sm text-yellow-800">
                                                            <strong>⚠️ Security:</strong> Client secret is hashed and cannot be displayed. 
                                                            If lost, you must regenerate it.
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="modal-action">
                                                    <form method="dialog">
                                                        <button class="btn">Close</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </dialog>

                                        <!-- Edit Modal -->
                                        <dialog id="editClient{{ $client->id }}Modal" class="modal">
                                            <div class="modal-box max-w-2xl">
                                                <h3 class="font-bold text-lg mb-4">Edit: {{ $client->name }}</h3>
                                                
                                                <livewire:admin.edit-o-auth-client :clientId="$client->id" :key="'edit-'.$client->id" />
                                            </div>
                                        </dialog>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Belum ada OAuth client. Buat yang pertama untuk memulai!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-mary-card>
        </div>
    </div>

    <!-- Create Client Modal -->
    <dialog id="create_client_modal" class="modal">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4">🔐 Buat OAuth Client Baru</h3>
            
            <livewire:admin.create-o-auth-client />
        </div>
    </dialog>

    @push('scripts')
    <script>
        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            Livewire.on('client-created', () => {
                // Close modal and reload page
                document.getElementById('create_client_modal').close();
                window.location.reload();
            });

            Livewire.on('client-updated', (event) => {
                // Close all edit modals and reload page
                document.querySelectorAll('dialog[id^="editClient"]').forEach(modal => modal.close());
                window.location.reload();
            });

            Livewire.on('close-edit-modal', () => {
                // Close all edit modals
                document.querySelectorAll('dialog[id^="editClient"]').forEach(modal => modal.close());
            });
        });
    </script>
    @endpush
</x-admin.layout>
