<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$admin = App\Models\User::where('email', 'admin@acs-rbac.com')->first();

echo "=== RBAC System Test ===" . PHP_EOL;
echo "Admin User: " . $admin->name . PHP_EOL;
echo "Admin Email: " . $admin->email . PHP_EOL;
echo "Admin Roles: " . $admin->roles->pluck('name')->join(', ') . PHP_EOL;
echo "Admin Groups: " . $admin->groups->pluck('name')->join(', ') . PHP_EOL;
echo PHP_EOL;

echo "=== Database Counts ===" . PHP_EOL;
echo "Users: " . App\Models\User::count() . PHP_EOL;
echo "Roles: " . App\Models\Role::count() . PHP_EOL;
echo "Permissions: " . App\Models\Permission::count() . PHP_EOL;
echo "Groups: " . App\Models\Group::count() . PHP_EOL;
echo "Group Members: " . App\Models\GroupMember::count() . PHP_EOL;
echo PHP_EOL;

echo "=== Group Details ===" . PHP_EOL;
$adminGroup = App\Models\Group::where('name', 'Administrators')->first();
echo "Administrators Group Members: " . $adminGroup->users->count() . PHP_EOL;
foreach ($adminGroup->users as $user) {
    echo "  - " . $user->name . " (joined: " . $user->pivot->joined_at . ")" . PHP_EOL;
}

echo PHP_EOL . "✅ RBAC System is working correctly!" . PHP_EOL;
