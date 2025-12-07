<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Passport\Client;

class UpdateOAuthClient extends Command
{
    protected $signature = 'oauth:update-client {client_id} {--add-uri=} {--remove-uri=} {--list}';
    protected $description = 'Update OAuth client redirect URIs';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        $client = Client::find($clientId);

        if (!$client) {
            $this->error("Client not found: {$clientId}");
            return 1;
        }

        if ($this->option('list')) {
            $this->info("Current redirect URIs for client: {$client->name}");
            foreach ($client->redirect_uris as $uri) {
                $this->line("  - {$uri}");
            }
            return 0;
        }

        if ($addUri = $this->option('add-uri')) {
            $uris = $client->redirect_uris;
            if (!in_array($addUri, $uris)) {
                $uris[] = $addUri;
                $client->redirect_uris = $uris;
                $client->save();
                $this->info("Added URI: {$addUri}");
            } else {
                $this->warn("URI already exists: {$addUri}");
            }
        }

        if ($removeUri = $this->option('remove-uri')) {
            $uris = array_filter($client->redirect_uris, fn($uri) => $uri !== $removeUri);
            $client->redirect_uris = array_values($uris);
            $client->save();
            $this->info("Removed URI: {$removeUri}");
        }

        $this->info("\nFinal redirect URIs:");
        foreach ($client->redirect_uris as $uri) {
            $this->line("  - {$uri}");
        }

        return 0;
    }
}
