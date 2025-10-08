/**
 * Smart Permission Management System
 * Handles permission dependencies and relationships with category-based check all/uncheck all
 */

class PermissionManager {
    constructor() {
        this.dependencies = {
            // System Management Dependencies
            manage_system: [], // Super admin permission - standalone
            manage_permissions: ["manage_system"], // Requires system management
            manage_roles: ["manage_permissions"], // Requires permission management

            // User Management Dependencies
            manage_users: ["view_users"], // Must be able to view users to manage them
            edit_user_roles: ["manage_users", "view_users"], // Requires user management
            view_users: [], // Basic permission

            // Group Management Dependencies
            manage_groups: ["view_groups"], // Must view groups to manage them
            assign_group_members: ["manage_groups", "view_groups"], // Requires group management
            manage_group_roles: ["manage_groups", "manage_roles"], // Requires both group and role management
            view_groups: [], // Basic permission

            // Content Management Dependencies
            delete_content: ["edit_content"], // Must edit to delete
            edit_content: ["create_content"], // Must create to edit
            publish_content: ["edit_content", "create_content"], // Must create/edit to publish
            create_content: [], // Basic permission

            // Reports Dependencies
            export_data: ["view_reports"], // Must view reports to export
            view_reports: [], // Basic permission

            // Department Management Dependencies
            manage_department: ["view_team_data"], // Must view team data to manage department
            approve_requests: ["view_team_data"], // Must view team data to approve requests
            view_team_data: [], // Basic permission

            // Profile Dependencies
            edit_profile: ["view_profile"], // Must view profile to edit it
            view_profile: [], // Basic permission
            view_dashboard: [], // Basic permission
        };

        this.nameMapping = {
            manage_system: "Manage System",
            manage_permissions: "Manage Permissions",
            manage_roles: "Manage Roles",
            manage_users: "Manage Users",
            view_users: "View Users",
            edit_user_roles: "Edit User Roles",
            manage_groups: "Manage Groups",
            view_groups: "View Groups",
            assign_group_members: "Assign Group Members",
            manage_group_roles: "Manage Group Roles",
            create_content: "Create Content",
            edit_content: "Edit Content",
            delete_content: "Delete Content",
            publish_content: "Publish Content",
            view_reports: "View Reports",
            export_data: "Export Data",
            view_dashboard: "View Dashboard",
            view_profile: "View Profile",
            edit_profile: "Edit Profile",
            manage_department: "Manage Department",
            view_team_data: "View Team Data",
            approve_requests: "Approve Requests",
        };

        this.categories = {
            system: ["manage_system", "manage_permissions", "manage_roles"],
            users: ["manage_users", "view_users", "edit_user_roles"],
            groups: ["manage_groups", "view_groups", "assign_group_members", "manage_group_roles"],
            content: ["create_content", "edit_content", "delete_content", "publish_content"],
            reports: ["view_reports", "export_data"],
            dashboard: ["view_dashboard"],
            profile: ["view_profile", "edit_profile"],
            department: ["manage_department"],
            team: ["view_team_data"],
            approvals: ["approve_requests"]
        };

        this.isProcessing = false; // Prevent infinite loops
    }

    /**
     * Initialize permission management for a form
     */
    init() {
        console.log('PermissionManager: Initializing...');
        this.bindEvents();
        this.addCategoryCheckboxes();
    }

    /**
     * Add check all/uncheck all buttons for each category
     */
    addCategoryCheckboxes() {
        // Find all category containers
        document.querySelectorAll('#permissions-container > div').forEach(categoryDiv => {
            const categoryTitle = categoryDiv.querySelector('h4');
            if (!categoryTitle) return;
            
            const categoryName = categoryTitle.textContent.trim().toLowerCase();
            
            // Skip if check all button already exists
            if (categoryDiv.querySelector('.category-check-all')) return;
            
            // Create check all container
            const checkAllContainer = document.createElement('div');
            checkAllContainer.className = 'flex items-center justify-between mb-2 category-check-all';
            
            // Create check all checkbox
            const checkAllCheckbox = document.createElement('input');
            checkAllCheckbox.type = 'checkbox';
            checkAllCheckbox.className = 'h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded';
            checkAllCheckbox.id = `check-all-${categoryName}`;
            
            const checkAllLabel = document.createElement('label');
            checkAllLabel.setAttribute('for', `check-all-${categoryName}`);
            checkAllLabel.className = 'ml-2 block text-sm font-medium text-gray-600';
            checkAllLabel.textContent = 'Check All';
            
            checkAllContainer.appendChild(checkAllCheckbox);
            checkAllContainer.appendChild(checkAllLabel);
            
            // Insert after the category title
            categoryTitle.parentNode.insertBefore(checkAllContainer, categoryTitle.nextSibling);
            
            // Bind check all event
            checkAllCheckbox.addEventListener('change', (e) => {
                this.handleCategoryCheckAll(categoryName, e.target.checked);
            });
            
            // Update check all state based on current selections
            this.updateCategoryCheckAllState(categoryName);
        });
    }

    /**
     * Handle category check all/uncheck all
     */
    handleCategoryCheckAll(categoryName, isChecked) {
        if (this.isProcessing) return;
        this.isProcessing = true;
        
        const permissions = this.categories[categoryName] || [];
        console.log(`Category ${categoryName} check all: ${isChecked}`, permissions);
        
        permissions.forEach(permissionName => {
            const permissionId = this.getPermissionIdByName(permissionName);
            if (permissionId) {
                const checkbox = document.querySelector(`input[value="${permissionId}"]`);
                if (checkbox && checkbox.checked !== isChecked) {
                    checkbox.checked = isChecked;
                    
                    // Handle dependencies
                    if (isChecked) {
                        this.handlePermissionChecked(permissionName, false);
                    } else {
                        this.handlePermissionUnchecked(permissionName, false);
                    }
                }
            }
        });
        
        // Update other category check all states
        setTimeout(() => {
            Object.keys(this.categories).forEach(cat => {
                if (cat !== categoryName) {
                    this.updateCategoryCheckAllState(cat);
                }
            });
            this.isProcessing = false;
        }, 100);
    }

    /**
     * Update category check all checkbox state
     */
    updateCategoryCheckAllState(categoryName) {
        const permissions = this.categories[categoryName] || [];
        const checkAllCheckbox = document.querySelector(`#check-all-${categoryName}`);
        
        if (!checkAllCheckbox) return;
        
        let checkedCount = 0;
        let totalCount = permissions.length;
        
        permissions.forEach(permissionName => {
            const permissionId = this.getPermissionIdByName(permissionName);
            if (permissionId) {
                const checkbox = document.querySelector(`input[value="${permissionId}"]`);
                if (checkbox && checkbox.checked) {
                    checkedCount++;
                }
            }
        });
        
        // Update check all checkbox state
        if (checkedCount === 0) {
            checkAllCheckbox.checked = false;
            checkAllCheckbox.indeterminate = false;
        } else if (checkedCount === totalCount) {
            checkAllCheckbox.checked = true;
            checkAllCheckbox.indeterminate = false;
        } else {
            checkAllCheckbox.checked = false;
            checkAllCheckbox.indeterminate = true;
        }
    }

    /**
     * Bind checkbox change events
     */
    bindEvents() {
        const checkboxes = document.querySelectorAll('input[wire\\:model="selectedPermissions"]');
        console.log(`PermissionManager: Found ${checkboxes.length} permission checkboxes`);
        
        checkboxes.forEach(checkbox => {
            // Remove existing listeners to avoid duplicates
            const newCheckbox = checkbox.cloneNode(true);
            checkbox.parentNode.replaceChild(newCheckbox, checkbox);
            
            // Add new listener
            newCheckbox.addEventListener('change', (e) => {
                if (this.isProcessing) return;
                
                const permissionId = e.target.value;
                const permissionName = this.getPermissionNameById(permissionId);
                
                console.log(`Permission change: ${permissionName} (${permissionId}) - ${e.target.checked}`);
                
                if (!permissionName) return;
                
                if (e.target.checked) {
                    this.handlePermissionChecked(permissionName, true);
                } else {
                    this.handlePermissionUnchecked(permissionName, true);
                }
            });
        });
    }

    /**
     * When a permission is checked, automatically check its dependencies
     */
    handlePermissionChecked(permissionName, updateCategories = true) {
        const dependencies = this.dependencies[permissionName] || [];
        console.log(`Checking dependencies for ${permissionName}:`, dependencies);
        
        // Auto-check all dependencies
        dependencies.forEach(depName => {
            const depId = this.getPermissionIdByName(depName);
            if (depId) {
                const depCheckbox = document.querySelector(`input[value="${depId}"]`);
                if (depCheckbox && !depCheckbox.checked) {
                    console.log(`Auto-checking dependency: ${depName}`);
                    depCheckbox.checked = true;
                    // Recursively check dependencies of    
                    this.handlePermissionChecked(depName, false);
                }
            }
        });
        
        if (updateCategories) {
            // Update category check all states
            setTimeout(() => {
                Object.keys(this.categories).forEach(categoryName => {
                    this.updateCategoryCheckAllState(categoryName);
                });
            }, 50);
        }
    }

    /**
     * When a permission is unchecked, uncheck permissions that depend on it
     */
    handlePermissionUnchecked(permissionName, updateCategories = true) {
        console.log(`Unchecking dependents of ${permissionName}`);
        
        // Find all permissions that depend on this one
        Object.entries(this.dependencies).forEach(([name, deps]) => {
            if (deps.includes(permissionName)) {
                const id = this.getPermissionIdByName(name);
                if (id) {
                    const checkbox = document.querySelector(`input[value="${id}"]`);
                    if (checkbox && checkbox.checked) {
                        console.log(`Auto-unchecking dependent: ${name}`);
                        checkbox.checked = false;
                        // Recursively uncheck dependencies
                        this.handlePermissionUnchecked(name, false);
                    }
                }
            }
        });
        
        if (updateCategories) {
            // Update category check all states
            setTimeout(() => {
                Object.keys(this.categories).forEach(categoryName => {
                    this.updateCategoryCheckAllState(categoryName);
                });
            }, 50);
        }
    }

    /**
     * Get permission name by ID from the label
     */
    getPermissionNameById(id) {
        const label = document.querySelector(`label[for="permission_${id}"]`);
        if (!label) {
            console.warn(`Label not found for permission ID: ${id}`);
            return null;
        }
        
        const displayName = label.textContent.trim();
        return this.displayNameToPermissionName(displayName);
    }

    /**
     * Get permission ID by name
     */
    getPermissionIdByName(name) {
        const displayName = this.nameMapping[name];
        if (!displayName) {
            console.warn(`Display name not found for permission: ${name}`);
            return null;
        }
        
        const labels = document.querySelectorAll('label[for^="permission_"]');
        
        for (let label of labels) {
            if (label.textContent.trim() === displayName) {
                const forAttr = label.getAttribute('for');
                return forAttr.replace('permission_', '');
            }
        }
        
        console.warn(`Permission ID not found for: ${name} (${displayName})`);
        return null;
    }

    /**
     * Convert display name to permission name
     */
    displayNameToPermissionName(displayName) {
        // Create reverse mapping
        const reverseMapping = Object.fromEntries(
            Object.entries(this.nameMapping).map(([key, value]) => [value, key])
        );
        return reverseMapping[displayName] || displayName.toLowerCase().replace(/\s+/g, '_');
    }
}

// Global permission manager instance
let permissionManager;

function initializePermissionManager() {
    console.log('Initializing Permission Manager...');
    permissionManager = new PermissionManager();
    
    // Wait for DOM to be fully ready
    setTimeout(() => {
        permissionManager.init();
    }, 200);
}

// Initialize on various events
document.addEventListener('DOMContentLoaded', initializePermissionManager);

// Re-initialize when Livewire updates
if (typeof Livewire !== 'undefined') {
    document.addEventListener('livewire:navigated', () => {
        console.log('Livewire navigated - reinitializing permission manager');
        setTimeout(initializePermissionManager, 300);
    });
    
    document.addEventListener('livewire:load', () => {
        console.log('Livewire loaded - reinitializing permission manager');
        setTimeout(initializePermissionManager, 300);
    });
}

// Also initialize when window loads (fallback)
window.addEventListener('load', () => {
    setTimeout(() => {
        if (!permissionManager) {
            console.log('Fallback initialization...');
            initializePermissionManager();
        }
    }, 500);
});
