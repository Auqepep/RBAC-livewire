<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <x-mary-icon name="o-key" class="w-6 h-6" />
                OAuth Client Apps
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
                            <div class="text-xs mt-2 flex items-center gap-1">
                                <x-mary-icon name="o-exclamation-circle" class="w-3 h-3" />
                                Save this secret now. You won't be able to see it again!
                            </div>
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
                                            <x-mary-icon name="o-clipboard-document-check" class="w-3 h-3" />
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
                                            <button onclick="document.getElementById('viewClient_{{ str_replace('-', '', $client->id) }}').showModal()" class="btn btn-info btn-xs gap-1">
                                                <x-mary-icon name="o-eye" class="w-3 h-3" />
                                                Lihat
                                            </button>
                                            <button onclick="document.getElementById('editClient_{{ str_replace('-', '', $client->id) }}').showModal()" class="btn btn-warning btn-xs gap-1">
                                                <x-mary-icon name="o-pencil" class="w-3 h-3" />
                                                Edit
                                            </button>
                                            @if(!$client->revoked)
                                                <form method="POST" action="{{ route('admin.oauth-clients.destroy', $client) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-error btn-xs gap-1" onclick="return confirm('Cabut akses client ini? Semua token akan dinonaktifkan.')">
                                                        <x-mary-icon name="o-no-symbol" class="w-3 h-3" />
                                                        Cabut
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- View Modal -->
                                        <dialog id="viewClient_{{ str_replace('-', '', $client->id) }}" class="modal">
                                            <div class="modal-box max-w-2xl">
                                                <form method="dialog">
                                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                </form>
                                                
                                                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                                                    <x-mary-icon name="o-eye" class="w-5 h-5" />
                                                    {{ $client->name }}
                                                </h3>
                                                
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="label font-semibold text-sm">Client ID</label>
                                                        <div class="flex gap-2">
                                                            <code class="flex-1 bg-gray-100 p-2 rounded text-sm break-all">{{ $client->id }}</code>
                                                            <button onclick="navigator.clipboard.writeText('{{ $client->id }}')" class="btn btn-sm btn-ghost" title="Copy">
                                                                <x-mary-icon name="o-clipboard-document-check" class="w-4 h-4" />
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="label font-semibold text-sm">Client Secret</label>
                                                        <div class="bg-gray-100 p-3 rounded">
                                                            <div class="flex gap-2 items-start">
                                                                <code class="flex-1 text-sm break-all">{{ $client->secret }}</code>
                                                                <button onclick="navigator.clipboard.writeText('{{ $client->secret }}')" class="btn btn-sm btn-ghost" title="Copy">
                                                                    <x-mary-icon name="o-clipboard-document-check" class="w-4 h-4" />
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">Simpan secret ini dengan aman. Anda bisa regenerate di menu Edit.</p>
                                                    </div>

                                                    <div>
                                                        <label class="label font-semibold text-sm">Redirect URIs</label>
                                                        <ul class="space-y-2">
                                                            @foreach($client->redirect_uris as $uri)
                                                                <li class="flex items-center gap-2 text-sm bg-gray-50 p-2 rounded">
                                                                    <x-mary-icon name="o-arrow-top-right-on-square" class="w-4 h-4 text-gray-400" />
                                                                    <span class="break-all">{{ $uri }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>

                                                    <div>
                                                        <label class="label font-semibold text-sm">Grant Types</label>
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach($client->grant_types as $type)
                                                                <span class="badge badge-neutral badge-sm">{{ $type }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="label font-semibold text-sm">Status</label>
                                                        @if($client->revoked)
                                                            <span class="badge badge-error">Revoked</span>
                                                        @else
                                                            <span class="badge badge-success">Active</span>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        <label class="label font-semibold text-sm">Created</label>
                                                        <p class="text-sm text-gray-600">{{ $client->created_at->format('F d, Y H:i') }}</p>
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
                                        <dialog id="editClient_{{ str_replace('-', '', $client->id) }}" class="modal">
                                            <div class="modal-box max-w-2xl">
                                                <form method="dialog">
                                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                                </form>
                                                
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
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                <x-mary-icon name="o-key" class="w-5 h-5" />
                Buat OAuth Client Baru
            </h3>
            
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
                document.querySelectorAll('dialog[id^="editClient_"]').forEach(modal => modal.close());
                window.location.reload();
            });

            Livewire.on('close-edit-modal', () => {
                // Close all edit modals
                document.querySelectorAll('dialog[id^="editClient_"]').forEach(modal => modal.close());
            });
        });
    </script>
    @endpush
</x-admin.layout>
