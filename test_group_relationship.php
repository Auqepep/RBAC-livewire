<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$group = \App\Models\Group::find(1);

if ($group) {
    echo "Group name: " . $group->name . "\n";
    echo "Users count: " . $group->users->count() . "\n";
    
    foreach ($group->users as $user) {
        echo "User: " . $user->name . "\n";
        if ($user->pivot) {
            echo "  Joined at: " . $user->pivot->joined_at . "\n";
            echo "  Added by: " . $user->pivot->added_by . "\n";
        } else {
            echo "  No pivot data\n";
        }
    }
} else {
    echo "Group not found\n";
}
