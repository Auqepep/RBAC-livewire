<?php

use Laravel\Passport\Client;

// Find the client
$client = Client::find('019addf9-0c08-7292-a1e8-96378b3ea2ba');

if ($client) {
    // Update redirect URIs to include httpbin.org
    $client->redirect_uris = [
        'http://localhost/auth/callback',
        'https://httpbin.org/get',
    ];
    $client->save();
    
    echo "✅ OAuth client updated successfully!\n";
    echo "Client ID: " . $client->id . "\n";
    echo "Redirect URIs:\n";
    foreach ($client->redirect_uris as $uri) {
        echo "  - " . $uri . "\n";
    }
} else {
    echo "❌ Client not found\n";
}
