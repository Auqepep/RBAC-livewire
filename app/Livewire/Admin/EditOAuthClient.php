<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Laravel\Passport\Client;

class EditOAuthClient extends Component
{
    public $clientId;
    public $name;
    public $redirect_uris = [];

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

    public function mount($clientId)
    {
        $client = Client::findOrFail($clientId);
        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->redirect_uris = $client->redirect_uris ?? [''];
    }

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

    public function updateClient()
    {
        $this->validate();

        // Remove empty URIs and re-index
        $cleanUris = array_values(array_filter($this->redirect_uris, fn($uri) => !empty($uri)));

        // Update the client
        $client = Client::findOrFail($this->clientId);
        $client->name = $this->name;
        $client->redirect_uris = $cleanUris;
        $client->save();

        // Show success message
        session()->flash('success', 'Client updated successfully!');

        // Dispatch event to refresh the main table
        $this->dispatch('client-updated');
    }

    public function regenerateSecret()
    {
        $client = Client::findOrFail($this->clientId);
        
        // Generate new secret (plain text)
        $newSecret = \Illuminate\Support\Str::random(40);
        
        // Bypass Passport's auto-hashing mutator by updating directly
        \Illuminate\Support\Facades\DB::table('oauth_clients')
            ->where('id', $client->id)
            ->update(['secret' => $newSecret]);

        // Flash the new secret to session
        session()->flash('success', 'Client secret regenerated successfully!');
        session()->flash('new_secret', $newSecret);
        session()->flash('client_id', $client->id);

        // Dispatch event to refresh and close modal
        $this->dispatch('client-updated');
    }

    public function render()
    {
        return view('livewire.admin.edit-o-auth-client');
    }
}
