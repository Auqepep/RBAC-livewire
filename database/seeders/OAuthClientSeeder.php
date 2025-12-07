<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Illuminate\Support\Str;

class OAuthClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if default client already exists
        $existingClient = Client::where('name', 'RBAC Default Client')->first();
        
        if ($existingClient) {
            $this->command->info('Default OAuth client already exists. Skipping...');
            return;
        }

        // Create default OAuth client for testing
        $client = new Client();
        $client->id = (string) Str::uuid();
        $client->name = 'RBAC Default Client';
        $client->secret = password_hash('rbac-client-secret-' . Str::random(40), PASSWORD_BCRYPT);
        $client->redirect_uris = [
            'https://httpbin.org/get',
            'http://localhost/auth/callback',
            'http://localhost:3000/callback',
        ];
        $client->grant_types = ['authorization_code', 'refresh_token'];
        $client->revoked = false;
        $client->save();

        $this->command->info('✅ Default OAuth client created successfully!');
        $this->command->line('');
        $this->command->line('Client ID: ' . $client->id);
        $this->command->line('Client Name: ' . $client->name);
        $this->command->line('Redirect URIs:');
        foreach ($client->redirect_uris as $uri) {
            $this->command->line('  - ' . $uri);
        }
        $this->command->line('');
        $this->command->warn('⚠️  Note: Client secret is hashed and cannot be retrieved.');
        $this->command->warn('    Use this client ID in your group settings.');
    }
}
