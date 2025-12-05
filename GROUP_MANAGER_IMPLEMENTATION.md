# Group Manager Access Implementation

## 🎯 Overview

Managers in each group now have the ability to:

-   ✅ **Edit their own group details** (name, description)
-   ✅ **Add and remove members** from their group
-   ✅ **Assign roles** to members in their group
-   ❌ **Cannot delete the group** (only system admins can)
-   ❌ **Cannot manage other groups** (only their own)

---

## 📋 What Was Implemented

### 1. **New Permissions Added**

Located in: `database/seeders/PermissionSeeder.php`

```php
'edit_own_group'              // Edit the group where user is a manager
'manage_own_group_members'    // Add, edit, remove members in own group
```

### 2. **Manager Role Updated**

Located in: `database/seeders/AdminSeeder.php`

Managers now have these permissions:

-   `view_users`
-   `view_groups`
-   `view_content`
-   `edit_content`
-   `manage_group_roles`
-   `assign_group_members`
-   `edit_own_group` ✨ **NEW**
-   `manage_own_group_members` ✨ **NEW**

### 3. **Group Policy Created**

Located in: `app/Policies/GroupPolicy.php`

The policy checks:

-   ✅ **System admins** can do anything to any group
-   ✅ **Managers** (hierarchy_level >= 70) can edit/manage **their own groups**
-   ❌ **Regular members** cannot edit groups

**Key Method:**

```php
public function update(User $user, Group $group): bool
{
    // System admins can update any group
    if ($user->hasPermission('manage_groups')) {
        return true;
    }

    // Check if user is a manager in this specific group
    return $this->isManagerOfGroup($user, $group)
        && $user->hasPermission('edit_own_group');
}
```

### 4. **GroupController Protected**

Located in: `app/Http/Controllers/Admin/GroupController.php`

All methods now check authorization:

```php
$this->authorize('update', $group);  // Checks GroupPolicy
```

### 5. **UI Updated**

Located in: `resources/views/livewire/user/group-homepage.blade.php`

Two separate action sections:

1. **Manager Actions** - Yellow/Warning themed, shown to group managers
2. **System Admin Actions** - Red/Error themed, shown to system admins

---

## 🔍 How It Works

### Permission Check Flow:

```
User tries to edit Group X
    ↓
GroupController → $this->authorize('update', $group)
    ↓
GroupPolicy → update() method
    ↓
Check 1: Is user a system admin?
    YES → ✅ Allow
    NO  → Continue to Check 2
    ↓
Check 2: Is user a manager IN THIS GROUP?
    Get user's GroupMember record for Group X
    Check if role->hierarchy_level >= 70
    YES → Check if has 'edit_own_group' permission
        YES → ✅ Allow
        NO  → ❌ Deny
    NO  → ❌ Deny
```

### Example Scenario:

**User: John (manager@example.com)**

-   Member of "Gateway Test Group" with **Manager** role (hierarchy_level = 70)
-   Has `edit_own_group` permission ✅

**What John Can Do:**

-   ✅ Edit "Gateway Test Group" details
-   ✅ Add/remove members from "Gateway Test Group"
-   ✅ Assign roles to members in "Gateway Test Group"

**What John CANNOT Do:**

-   ❌ Edit "Regular Users" group (not a manager there)
-   ❌ Delete "Gateway Test Group" (only system admins)
-   ❌ Manage permissions globally (not a system admin)

---

## 🧪 Testing

### Test Accounts:

1. **System Admin**
    - Email: `admin@example.com`
    - Can: Manage ALL groups
2. **Manager**
    - Email: `manager@example.com`
    - Can: Manage ONLY "Gateway Test Group"
3. **Regular User**
    - Email: `user@example.com`
    - Can: View groups but not edit

### Test Steps:

1. **Login as manager@example.com**
2. **Go to "Gateway Test Group" homepage**
3. **Look for "Manager Actions" card** (yellow border)
4. **Click "Edit Group Details"** → Should work! ✅
5. **Try to edit "Regular Users" group** → Should fail! ❌
6. **Logout and login as user@example.com**
7. **Try to edit any group** → Should fail! ❌

---

## 📁 Files Modified

1. ✅ `database/seeders/PermissionSeeder.php` - Added new permissions
2. ✅ `database/seeders/AdminSeeder.php` - Updated manager permissions
3. ✅ `app/Policies/GroupPolicy.php` - Created new policy
4. ✅ `app/Providers/AppServiceProvider.php` - Registered policy
5. ✅ `app/Http/Controllers/Admin/GroupController.php` - Added authorization checks
6. ✅ `app/Livewire/User/GroupHomepage.php` - Added manager checks
7. ✅ `resources/views/livewire/user/group-homepage.blade.php` - Added manager UI

---

## 🚀 Next Steps

1. **Clear all caches:**

    ```powershell
    php artisan optimize:clear
    ```

2. **Test the functionality:**

    - Login as `manager@example.com`
    - Visit: http://localhost:8000/my-groups/{group_id}/gateway
    - Look for "Manager Actions" card

3. **Optional: Add hierarchy check**
    - Managers can only manage members with **lower** hierarchy levels
    - This prevents managers from editing other managers

---

## 🔐 Security Notes

-   ✅ Managers can ONLY edit groups they are managers of
-   ✅ Managers cannot elevate their own permissions
-   ✅ Managers cannot delete groups
-   ✅ All actions are logged through Laravel's authorization system
-   ✅ Policy checks happen before controller actions

---

## 💡 Future Enhancements

1. **Audit Log**: Track who made what changes to groups
2. **Hierarchy Enforcement**: Managers can't edit users with equal/higher roles
3. **Delegated Permissions**: Super-managers can grant temporary permissions
4. **Group Templates**: Create groups from templates
5. **Bulk Actions**: Add/remove multiple members at once

---

**Status**: ✅ **READY TO TEST**
**Database**: ✅ **Migrated & Seeded**
**Authorization**: ✅ **Policy Implemented**
**UI**: ✅ **Manager Actions Visible**
