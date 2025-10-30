<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed the new group-centric RBAC structure
        $this->call([
            PermissionSeeder::class,
            AdminSeeder::class,
            // GroupCentricSeeder::class, // Commented out to avoid conflicts
        ]);
    }
}
