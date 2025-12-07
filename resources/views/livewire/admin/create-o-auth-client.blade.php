<div>
    <form wire:submit="createClient" class="space-y-6">
        <!-- Client Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Aplikasi <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   wire:model="name" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Contoh: My Mobile App">
            @error('name') 
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Redirect URIs -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Redirect URI <span class="text-red-500">*</span>
            </label>
            <p class="text-sm text-gray-500 mb-3">📌 Tambahkan satu atau lebih URI untuk callback OAuth. <span class="font-semibold">1 Client ID bisa punya BANYAK Redirect URI!</span></p>
            
            @foreach($redirect_uris as $index => $uri)
                <div class="flex gap-2 mb-2">
                    <input type="url" 
                           wire:model="redirect_uris.{{ $index }}" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="https://aplikasiku.com/auth/callback">
                    
                    @if(count($redirect_uris) > 1)
                        <button type="button" 
                                wire:click="removeRedirectUri({{ $index }})"
                                class="btn btn-error btn-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    @endif
                </div>
                @error("redirect_uris.{$index}") 
                    <p class="mt-1 mb-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            @endforeach

            <button type="button" 
                    wire:click="addRedirectUri"
                    class="btn btn-outline btn-sm mt-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('Add URI') }}
            </button>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" 
                    onclick="document.getElementById('create_client_modal').close()"
                    class="btn btn-ghost">
                {{ __('Cancel') }}
            </button>
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="btn btn-primary">
                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove>{{ __('Create Client') }}</span>
                <span wire:loading>{{ __('Creating...') }}</span>
            </button>
        </div>
    </form>

    <!-- Secret Display Modal -->
    @if($showSecretModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">✅ Client Berhasil Dibuat!</h3>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 font-semibold">⚠️ Penting: Simpan Secret Sekarang!</p>
                            <p class="text-sm text-yellow-700 mt-1">Secret ini hanya ditampilkan sekali. Simpan di tempat yang aman!</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   value="{{ $createdClientId }}" 
                                   readonly 
                                   class="flex-1 px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg font-mono text-sm">
                            <button type="button" 
                                    onclick="this.innerHTML='✓ Tersalin!'; navigator.clipboard.writeText('{{ $createdClientId }}'); setTimeout(() => this.innerHTML='📋 Salin', 2000)"
                                    class="btn btn-success btn-sm">
                                📋 Salin
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client Secret <span class="text-red-500">(Rahasia - Jangan Share!)</span></label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   value="{{ $generatedSecret }}" 
                                   readonly 
                                   class="flex-1 px-4 py-2 bg-yellow-50 border-2 border-yellow-300 rounded-lg font-mono text-sm text-red-600 font-semibold">
                            <button type="button" 
                                    onclick="this.innerHTML='✓ Tersalin!'; navigator.clipboard.writeText('{{ $generatedSecret }}'); setTimeout(() => this.innerHTML='📋 Salin', 2000)"
                                    class="btn btn-success btn-sm">
                                📋 Salin
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">💡 Simpan di .env file atau secret manager</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" 
                            wire:click="closeSecretModal"
                            class="btn btn-primary">
                        ✓ {{ __('I Saved It') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
