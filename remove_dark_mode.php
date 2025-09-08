<?php

$files = [
    'resources/views/admin/roles/edit.blade.php',
    'resources/views/admin/roles/show.blade.php',
    'resources/views/admin/users/edit.blade.php',
    'resources/views/admin/groups/edit.blade.php',
    'resources/views/admin/groups/show.blade.php',
    'resources/views/livewire/welcome/navigation.blade.php',
    'resources/views/livewire/pages/auth/confirm-password.blade.php',
    'resources/views/livewire/profile/delete-user-form.blade.php',
    'resources/views/livewire/layout/navigation.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/guest.blade.php',
    'resources/views/livewire/pages/auth/forgot-password.blade.php',
    'resources/views/components/action-message.blade.php',
    'resources/views/livewire/profile/update-password-form.blade.php',
    'resources/views/livewire/profile/update-profile-information-form.blade.php',
    'resources/views/livewire/pages/auth/login.blade.php',
    'resources/views/components/danger-button.blade.php'
];

$replacements = [
    ' dark:text-gray-200' => '',
    ' dark:bg-gray-800' => '',
    ' dark:text-gray-300' => '',
    ' dark:bg-gray-700' => '',
    ' dark:border-gray-600' => '',
    ' dark:text-white' => '',
    ' dark:text-gray-100' => '',
    ' dark:text-gray-400' => '',
    ' dark:border-gray-700' => '',
    ' dark:hover:bg-gray-700' => '',
    ' dark:divide-gray-700' => '',
    ' dark:text-gray-500' => '',
    ' dark:hover:text-gray-400' => '',
    ' dark:hover:bg-gray-900' => '',
    ' dark:focus:bg-gray-900' => '',
    ' dark:focus:text-gray-400' => '',
    ' dark:border-gray-600' => '',
    ' dark:bg-gray-900' => '',
    ' dark:focus:ring-indigo-600' => '',
    ' dark:focus:ring-offset-gray-800' => '',
    ' dark:hover:text-gray-100' => '',
    ' dark:hover:text-white/80' => '',
    ' dark:focus-visible:ring-white' => '',
    ' dark:text-green-400' => '',
    ' dark:text-red-400' => ''
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $originalContent = $content;
        
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "Updated: $file\n";
        } else {
            echo "No changes needed: $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}

echo "Dark mode removal completed!\n";
