<x-user.layout>
    <x-mary-header 
        title="Permission Testing" 
        subtitle="Test your current permissions in real-time"
        size="text-2xl"
        separator
    >
        <x-slot:actions>
            <x-mary-button 
                id="refresh-permissions"
                label="Refresh" 
                icon="o-arrow-path"
                class="btn-primary"
                onclick="refreshPermissions()"
            />
        </x-slot:actions>
    </x-mary-header>

    <div class="space-y-6">
        <!-- User Info -->
        <x-mary-card title="Current User" shadow>
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-lg font-bold text-white">
                        {{ substr($user->name, 0, 1) }}
                    </span>
                </div>
                <div>
                    <h3 class="text-lg font-medium">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400">User ID: {{ $user->id }}</p>
                </div>
                <div class="ml-auto">
                    @if($user->canManageSystem())
                        <x-mary-badge value="System Admin" class="badge-error" />
                    @endif
                    <div class="text-xs text-gray-500 mt-1">
                        Last updated: <span id="last-updated">{{ now()->format('H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </x-mary-card>

        <!-- Permission Test Results -->
        @foreach($permissionsByCategory as $category => $permissions)
            <x-mary-card title="{{ ucfirst($category) }} Permissions" shadow separator>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissions as $permission)
                        <div class="permission-test-item border rounded-lg p-4 transition-all duration-300" 
                             data-permission="{{ $permission->name }}"
                             id="permission-{{ $permission->name }}">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $permission->display_name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $permission->description }}</p>
                                    <code class="text-xs text-gray-500 font-mono">{{ $permission->name }}</code>
                                </div>
                                <div class="ml-3">
                                    <div class="permission-status" id="status-{{ $permission->name }}">
                                        @if(auth()->user()->can($permission->name))
                                            <x-mary-badge value="✓ Allowed" class="badge-success" />
                                        @else
                                            <x-mary-badge value="✗ Denied" class="badge-error" />
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <x-mary-button 
                                    label="Test Now" 
                                    class="btn-sm btn-outline w-full"
                                    onclick="testSinglePermission('{{ $permission->name }}')"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-mary-card>
        @endforeach

        <!-- Real-time Testing Instructions -->
        <x-mary-card title="How to Test Permissions" shadow>
            <div class="prose max-w-none">
                <ol class="list-decimal list-inside space-y-2">
                    <li><strong>As Admin:</strong> Open the <a href="{{ route('admin.permissions.index') }}" target="_blank" class="text-blue-600 hover:underline">Admin Permissions Page</a> in a new tab</li>
                    <li><strong>Change Permissions:</strong> Modify user roles or permissions using the admin interface</li>
                    <li><strong>Test Changes:</strong> Click "Refresh" or "Test Now" buttons on this page to see changes immediately</li>
                    <li><strong>Real-time Updates:</strong> Changes should reflect within seconds without page reload</li>
                </ol>
                
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-semibold text-blue-900">Quick Test Links:</h4>
                    <div class="mt-2 space-x-2">
                        @if(auth()->user()->canManageSystem())
                            <x-mary-button label="Admin Dashboard" link="{{ route('admin.dashboard') }}" target="_blank" class="btn-sm btn-primary" />
                            <x-mary-button label="Manage Users" link="{{ route('admin.users.index') }}" target="_blank" class="btn-sm btn-secondary" />
                            <x-mary-button label="Manage Permissions" link="{{ route('admin.permissions.index') }}" target="_blank" class="btn-sm btn-accent" />
                        @else
                            <x-mary-button label="User Dashboard" link="{{ route('dashboard') }}" target="_blank" class="btn-sm btn-primary" />
                            <x-mary-button label="My Groups" link="{{ route('groups.index') }}" target="_blank" class="btn-sm btn-secondary" />
                        @endif
                    </div>
                </div>
            </div>
        </x-mary-card>
    </div>

    <!-- Auto-refresh toggle -->
    <div class="fixed bottom-4 right-4">
        <x-mary-card class="w-64 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium">Auto-refresh</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="auto-refresh-toggle" class="sr-only peer" onchange="toggleAutoRefresh()">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            <div class="text-xs text-gray-500 mt-1">
                Refreshes every <span id="refresh-interval">10</span> seconds
            </div>
        </x-mary-card>
    </div>

    <script>
        let autoRefreshInterval = null;
        let refreshIntervalSeconds = 10;

        // Test a single permission
        function testSinglePermission(permissionName) {
            fetch('{{ route("test.permission") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ permission: permissionName })
            })
            .then(response => response.json())
            .then(data => {
                updatePermissionStatus(permissionName, data.allowed);
                showNotification(`Permission "${permissionName}": ${data.allowed ? 'ALLOWED' : 'DENIED'}`, data.allowed ? 'success' : 'error');
            })
            .catch(error => {
                console.error('Error testing permission:', error);
                showNotification('Error testing permission', 'error');
            });
        }

        // Refresh all permissions
        function refreshPermissions() {
            const permissions = document.querySelectorAll('[data-permission]');
            const permissionNames = Array.from(permissions).map(el => el.dataset.permission);
            
            // Show loading state
            document.getElementById('refresh-permissions').innerHTML = '<span class="loading loading-spinner loading-sm"></span> Refreshing...';
            
            // Test each permission
            Promise.all(permissionNames.map(permission => 
                fetch('{{ route("test.permission") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ permission })
                }).then(r => r.json())
            ))
            .then(results => {
                results.forEach(data => {
                    updatePermissionStatus(data.permission, data.allowed);
                });
                
                // Update last updated time
                document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
                
                // Reset button
                document.getElementById('refresh-permissions').innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Refresh';
                
                showNotification(`Refreshed ${results.length} permissions`, 'success');
            })
            .catch(error => {
                console.error('Error refreshing permissions:', error);
                showNotification('Error refreshing permissions', 'error');
                document.getElementById('refresh-permissions').innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Refresh';
            });
        }

        // Update permission status in UI
        function updatePermissionStatus(permissionName, allowed) {
            const statusElement = document.getElementById(`status-${permissionName}`);
            const itemElement = document.getElementById(`permission-${permissionName}`);
            
            if (statusElement && itemElement) {
                if (allowed) {
                    statusElement.innerHTML = '<span class="badge badge-success">✓ Allowed</span>';
                    itemElement.className = itemElement.className.replace('border-red-200 bg-red-50', '').replace('border-gray-200', '') + ' border-green-200 bg-green-50';
                } else {
                    statusElement.innerHTML = '<span class="badge badge-error">✗ Denied</span>';
                    itemElement.className = itemElement.className.replace('border-green-200 bg-green-50', '').replace('border-gray-200', '') + ' border-red-200 bg-red-50';
                }
            }
        }

        // Toggle auto-refresh
        function toggleAutoRefresh() {
            const toggle = document.getElementById('auto-refresh-toggle');
            
            if (toggle.checked) {
                autoRefreshInterval = setInterval(refreshPermissions, refreshIntervalSeconds * 1000);
                showNotification(`Auto-refresh enabled (every ${refreshIntervalSeconds}s)`, 'success');
            } else {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                }
                showNotification('Auto-refresh disabled', 'info');
            }
        }

        // Show notification
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert ${type === 'success' ? 'alert-success' : type === 'error' ? 'alert-error' : 'alert-info'} fixed top-4 right-4 z-50 w-auto max-w-sm shadow-lg`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-lg">&times;</button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 3000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add CSRF token to meta if not present
            if (!document.querySelector('meta[name="csrf-token"]')) {
                const meta = document.createElement('meta');
                meta.name = 'csrf-token';
                meta.content = '{{ csrf_token() }}';
                document.head.appendChild(meta);
            }
        });
    </script>
</x-user.layout>
