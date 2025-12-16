<div>
    <form wire:submit="updateClient" class="space-y-6">
        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm text-green-700">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        <!-- Client ID (Read-only) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Client ID <span class="text-gray-400 text-xs">(tidak bisa diubah)</span></label>
            <input type="text" 
                   value="{{ $clientId }}" 
                   readonly 
                   class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-mono text-sm cursor-not-allowed">
        </div>

        <!-- Client Name -->
        <div>
            <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Aplikasi <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="edit_name" 
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
            <p class="text-sm text-gray-500 mb-3">📝 Update URI untuk callback OAuth. <span class="font-semibold">1 Client ID bisa punya BANYAK Redirect URI!</span></p>
            
            @foreach($redirect_uris as $index => $uri)
                <div class="flex gap-2 mb-2">
                    <input type="url" 
                           wire:model="redirect_uris.{{ $index }}" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="https://example.com/callback">
                    
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

        <!-- Regenerate Secret Section -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h4 class="font-semibold text-sm text-yellow-900 mb-2 flex items-center gap-2">
                <x-mary-icon name="o-key" class="w-4 h-4" />
                Client Secret
            </h4>
            <p class="text-sm text-yellow-800 mb-3">
                Client secret tidak bisa ditampilkan untuk keamanan. Jika lupa atau perlu menggantinya, klik tombol di bawah untuk generate secret baru.
            </p>
            <button type="button" 
                    wire:click="regenerateSecret"
                    wire:confirm="Regenerate client secret? Secret lama tidak akan bisa digunakan lagi!"
                    class="btn btn-warning btn-sm">
                <x-mary-icon name="o-arrow-path" class="w-4 h-4" />
                Regenerate Secret
            </button>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" 
                    onclick="this.closest('dialog').close()"
                    class="btn btn-ghost">
                {{ __('Cancel') }}
            </button>
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="btn btn-primary">
                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove>{{ __('Save Changes') }}</span>
                <span wire:loading>{{ __('Saving') }}...</span>
            </button>
        </div>
    </form>
</div>
