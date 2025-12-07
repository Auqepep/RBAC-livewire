<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Laravel\Passport\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OAuthClientController extends Controller
{
    public function index()
    {
        $clients = Client::where('revoked', false)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.oauth-clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.oauth-clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'redirect_uris' => 'required|array|min:1',
            'redirect_uris.*' => 'required|url',
        ]);

        $client = new Client();
        $client->id = (string) Str::uuid();
        $client->name = $validated['name'];
        $client->secret = password_hash(Str::random(60), PASSWORD_BCRYPT);
        $client->redirect_uris = $validated['redirect_uris'];
        $client->grant_types = ['authorization_code', 'refresh_token'];
        $client->revoked = false;
        $client->save();

        // Store plain secret temporarily for display
        session()->flash('client_secret', Str::random(60));

        return redirect()->route('admin.oauth-clients.index')
            ->with('success', 'OAuth client created successfully!')
            ->with('client_id', $client->id);
    }

    public function destroy(Client $client)
    {
        $client->revoked = true;
        $client->save();

        return redirect()->route('admin.oauth-clients.index')
            ->with('success', 'OAuth client revoked successfully.');
    }

    public function regenerateSecret(Client $client)
    {
        $newSecret = Str::random(60);
        $client->secret = password_hash($newSecret, PASSWORD_BCRYPT);
        $client->save();

        return redirect()->route('admin.oauth-clients.index')
            ->with('success', 'Client secret regenerated successfully!')
            ->with('new_secret', $newSecret)
            ->with('client_id', $client->id);
    }
}
