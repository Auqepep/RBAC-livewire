<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;

class CreateOAuthClient extends Component
{
    public $name = '';
    public $redirect_uris = [''];
    public $showSecretModal = false;
    public $generatedSecret = null;
    public $createdClientId = null;
    public $isSubmitting = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'redirect_uris' => 'required|array|min:1',
        'redirect_uris.*' => 'required|url',
    ];

    protected $messages = [
        'name.required' => 'Client name is required',
        'redirect_uris.required' => 'At least one redirect URI is required',
        'redirect_uris.*.required' => 'All redirect URIs must be filled',
        'redirect_uris.*.url' => 'Each redirect URI must be a valid URL',
    ];

    public function addRedirectUri()
    {
        $this->redirect_uris = array_values($this->redirect_uris);
        $this->redirect_uris[] = '';
    }

    public function removeRedirectUri($index)
    {
        if (count($this->redirect_uris) > 1) {
            unset($this->redirect_uris[$index]);
            $this->redirect_uris = array_values($this->redirect_uris);
        }
    }

    public function createClient()
    {
        $this->isSubmitting = true;
        
        $this->validate();

        // Remove empty URIs and re-index array
        $cleanUris = array_values(array_filter($this->redirect_uris, fn($uri) => !empty($uri)));

        // Generate client secret (plain) - 40 characters random string
        $plainSecret = Str::random(40);
        $clientId = Str::uuid()->toString();
        
        // Insert directly to bypass Passport's auto-hashing mutator
        \Illuminate\Support\Facades\DB::table('oauth_clients')->insert([
            'id' => $clientId,
            'name' => $this->name,
            'secret' => $plainSecret,
            'redirect' => json_encode($cleanUris),
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Store for display (one-time only)
        $this->generatedSecret = $plainSecret;
        $this->createdClientId = $clientId;
        
        // Show modal with secret
        $this->showSecretModal = true;

        // Reset form
        $this->name = '';
        $this->redirect_uris = [''];
        $this->isSubmitting = false;

        // Dispatch event to refresh the main table
        $this->dispatch('client-created');
    }

    public function closeSecretModal()
    {
        $this->showSecretModal = false;
        $this->generatedSecret = null;
        $this->createdClientId = null;
    }

    public function render()
    {
        return view('livewire.admin.create-o-auth-client');
    }
}
