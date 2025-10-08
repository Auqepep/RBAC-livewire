/**
 * Permission Manager - Dynamic Database-Driven
 * Loads permission dependencies dynamically from server data
 */

window.PermissionManager = {
    dependencies: {},
    permissions: [],
    isUpdating: false,

    async init() {
        console.log('Permission Manager initialized - Dynamic mode');
        console.log('Current URL:', window.location.href);
        console.log('DOM ready state:', document.readyState);
        
        // Check if we're on the right page
        if (!document.querySelector('#permissions-container')) {
            console.warn('Permissions container not found - skipping initialization');
            return;
        }
        
        await this.loadPermissions();
        this.bindPermissionEvents();
        
        console.log('Initialization complete. Dependencies loaded:', this.dependencies);
    },

    async loadPermissions() {
        try {
            // Try to fetch from API first
            const response = await fetch('/api/permissions');
            if (response.ok) {
                const permissions = await response.json();
                this.processPermissions(permissions);
                console.log('Permissions loaded from API:', this.dependencies);
            } else {
                throw new Error('API not available');
            }
        } catch (error) {
            console.warn('API failed, extracting from DOM:', error.message);
            this.extractFromDOM();
        }
    },

    processPermissions(permissions) {
        this.permissions = permissions;
        this.dependencies = {};

        permissions.forEach(permission => {
            if (permission.dependencies && permission.dependencies.length > 0) {
                // Convert dependency names to display names
                const depDisplayNames = permission.dependencies.map(depName => {
                    const depPerm = permissions.find(p => p.name === depName);
                    return depPerm ? depPerm.display_name : depName;
                });
                this.dependencies[permission.display_name] = depDisplayNames;
            }
        });
    },

    extractFromDOM() {
        console.log('Extracting permission structure from DOM...');
        // Simple dependency mapping for fallback
        this.dependencies = {
            'Manage Permissions': ['Manage System'],
            'Manage Roles': ['Manage Permissions'],
            'Manage Users': ['View Users'],
            'Edit User Roles': ['Manage Users', 'View Users'],
            'Manage Groups': ['View Groups'],
            'Assign Group Members': ['Manage Groups', 'View Groups'],
            'Manage Group Roles': ['Manage Groups', 'Manage Roles'],
            'Edit Content': ['Create Content'],
            'Delete Content': ['Edit Content'],
            'Publish Content': ['Edit Content', 'Create Content'],
            'Export Data': ['View Reports'],
            'Manage Department': ['View Team Data'],
            'Approve Requests': ['View Team Data'],
            'Edit Profile': ['View Profile']
        };
    },

    bindPermissionEvents() {
        const checkboxes = document.querySelectorAll('input[wire\\:model\\.defer="selectedPermissions"]');
        console.log(`Found ${checkboxes.length} permission checkboxes`);

        if (checkboxes.length === 0) {
            console.warn('No permission checkboxes found! Trying alternative selectors...');
            // Try alternative selectors
            const altCheckboxes1 = document.querySelectorAll('input[wire\\:model="selectedPermissions"]');
            const altCheckboxes2 = document.querySelectorAll('input[type="checkbox"].permission-checkbox');
            console.log(`Alternative wire:model found ${altCheckboxes1.length} checkboxes`);
            console.log(`Alternative .permission-checkbox found ${altCheckboxes2.length} checkboxes`);
            
            // Use alternative if found
            if (altCheckboxes1.length > 0) {
                this.bindEventsToCheckboxes(altCheckboxes1);
            } else if (altCheckboxes2.length > 0) {
                this.bindEventsToCheckboxes(altCheckboxes2);
            }
            return;
        }

        this.bindEventsToCheckboxes(checkboxes);
    },

    bindEventsToCheckboxes(checkboxes) {
        checkboxes.forEach((checkbox, index) => {
            const label = document.querySelector(`label[for="${checkbox.id}"]`);
            const permissionName = label ? label.textContent.trim() : `Unknown_${index}`;
            
            console.log(`Binding event for: ${permissionName} (ID: ${checkbox.id}, Value: ${checkbox.value})`);
            
            // Remove existing listeners to prevent duplicates
            checkbox.removeEventListener('change', this.handlePermissionChange);
            
            // Add new listener with proper context
            checkbox.addEventListener('change', (e) => {
                console.log(`CHECKBOX CHANGED: ${permissionName} -> ${e.target.checked}`);
                this.handlePermissionChange(e, permissionName);
            });
            
            // Test if checkbox is working at all
            checkbox.addEventListener('click', () => {
                console.log(`CHECKBOX CLICKED: ${permissionName}`);
            });
        });
        
        console.log('All event listeners bound successfully');
    },

    handlePermissionChange(event, permissionName) {
        if (this.isUpdating) {
            console.log(`Skipping update for ${permissionName} - isUpdating flag is true`);
            return;
        }
        
        console.log(`Permission ${permissionName} changed to: ${event.target.checked}`);
        
        if (event.target.checked) {
            this.checkDependencies(permissionName);
        } else {
            this.uncheckDependents(permissionName);
        }
        
        // Let Livewire handle the update naturally
        console.log(`Livewire will sync ${permissionName}`);
    },

    checkDependencies(permissionName) {
        const deps = this.dependencies[permissionName] || [];
        
        if (deps.length === 0) {
            console.log(`No dependencies for ${permissionName}`);
            return;
        }
        
        console.log(`Checking dependencies for ${permissionName}:`, deps);
        
        this.isUpdating = true;
        
        deps.forEach(depName => {
            const depCheckbox = this.findCheckboxByPermission(depName);
            if (depCheckbox && !depCheckbox.checked) {
                console.log(`Auto-checking dependency: ${depName}`);
                depCheckbox.checked = true;
                
                // Recursively check dependencies of dependencies
                this.checkDependencies(depName);
            }
        });
        
        this.isUpdating = false;
    },

    uncheckDependents(permissionName) {
        console.log(`Finding dependents of ${permissionName}`);
        
        this.isUpdating = true;
        
        // Find permissions that depend on this one
        Object.entries(this.dependencies).forEach(([permission, deps]) => {
            if (deps.includes(permissionName)) {
                const checkbox = this.findCheckboxByPermission(permission);
                if (checkbox && checkbox.checked) {
                    console.log(`Auto-unchecking dependent: ${permission}`);
                    checkbox.checked = false;
                    
                    // Recursively uncheck dependents of dependents
                    this.uncheckDependents(permission);
                }
            }
        });
        
        this.isUpdating = false;
    },

    findCheckboxByPermission(permissionName) {
        const labels = document.querySelectorAll('label[for^="permission_"]');
        
        for (let label of labels) {
            if (label.textContent.trim() === permissionName) {
                const checkboxId = label.getAttribute('for');
                return document.getElementById(checkboxId);
            }
        }
        
        console.warn(`Checkbox not found for permission: ${permissionName}`);
        return null;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Content Loaded - Checking for permissions container...');
    
    setTimeout(() => {
        const container = document.querySelector('#permissions-container');
        console.log('Permissions container check:', container ? 'FOUND' : 'NOT FOUND');
        
        if (container) {
            console.log('Initializing Permission Manager...');
            PermissionManager.init().catch(error => {
                console.error('Permission Manager initialization failed:', error);
            });
        } else {
            console.log('Permissions container not found - skipping initialization');
            // Try alternative containers
            const form = document.querySelector('form[wire\\:submit="save"]');
            console.log('Form found:', form ? 'YES' : 'NO');
            
            const checkboxes = document.querySelectorAll('input[wire\\:model="selectedPermissions"]');
            console.log('Permission checkboxes found:', checkboxes.length);
        }
    }, 1000); // Increased delay to ensure Livewire is ready
});

// Livewire compatibility
if (typeof Livewire !== 'undefined') {
    console.log('Livewire detected - adding event listeners');
    
    document.addEventListener('livewire:navigated', () => {
        console.log('Livewire navigated event fired');
        setTimeout(() => {
            if (document.querySelector('#permissions-container')) {
                console.log('Livewire navigated, reinitializing...');
                PermissionManager.init();
            }
        }, 1000);
    });
    
    // Additional Livewire hooks for debugging
    document.addEventListener('livewire:load', () => {
        console.log('Livewire loaded event fired');
    });
    
    document.addEventListener('livewire:update', () => {
        console.log('Livewire update event fired');
    });
} else {
    console.log('Livewire NOT detected');
}
