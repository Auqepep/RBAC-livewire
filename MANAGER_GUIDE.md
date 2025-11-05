# 🎯 Manager Guide: How to Manage Your Group

## Overview

As a **Manager** in a group, you have elevated permissions to manage the group's details and members. This guide explains what you can do and how to do it.

---

## 🔑 Manager Permissions

### What Managers CAN Do:

1. ✅ **Edit Group Details**

    - Change the group name
    - Update the group description
    - Activate/deactivate the group

2. ✅ **Manage Members**

    - Add new members to the group
    - Remove members from the group
    - Change member roles (to Staff or Member)

3. ✅ **View All Group Information**

    - See all group members
    - View member roles and join dates
    - Access group statistics

4. ✅ **Access Gateway Features**
    - Use the group gateway (if enabled)

### What Managers CANNOT Do:

-   ❌ Assign other users as **Manager** or **Administrator** (only admins can do this)
-   ❌ Delete the group entirely
-   ❌ Remove themselves if they're the only manager
-   ❌ Promote users to roles higher than their own hierarchy level

---

## 📋 Step-by-Step Instructions

### 1️⃣ Access Your Group

1. Log in to your account
2. Navigate to **Dashboard** → **My Groups**
3. Click on the group you manage

### 2️⃣ Open the Management Panel

On the group homepage, you'll see a **"Manager Actions"** card with a button:

-   Click **"Manage Group"** to open the management panel

### 3️⃣ Edit Group Details

In the management panel:

1. Update the **Group Name** (must be unique)
2. Modify the **Description** to explain the group's purpose
3. Toggle **Active** status (inactive groups are hidden)
4. Click **"Save Changes"**

### 4️⃣ Add New Members

To add a member:

1. Click the **"Add Member"** button (top right of members table)
2. Use the search box to find users by name or email
3. Select the user from the dropdown
4. Choose their **Role**:
    - **Member** (Level 5) - Basic access
    - **Staff** (Level 10) - Enhanced access with gateway permissions
    - **Manager** (Level 50) - Same as your role (only if you're admin)
5. Click **"Add Member"**

### 5️⃣ Change Member Roles

To update a member's role:

1. Find the member in the members table
2. Use the **dropdown menu** in their row
3. Select the new role
4. The change is applied immediately

**Note:** You can only assign roles at or below your hierarchy level.

### 6️⃣ Remove Members

To remove a member:

1. Find the member in the members table
2. Click the **🗑️ trash icon** on the right
3. Confirm the removal

**Important:** You cannot remove yourself if you're the only manager/admin.

---

## 🎨 Role Hierarchy Explained

Our system uses a hierarchy to control who can manage whom:

| Role          | Level | Badge Color | Can Be Assigned By |
| ------------- | ----- | ----------- | ------------------ |
| Administrator | 100   | Red         | System Admins Only |
| Manager       | 50    | Orange      | Admins & Managers  |
| Staff         | 10    | Blue        | Managers+          |
| Member        | 5     | Gray        | Managers+          |

**Hierarchy Rule:** You can only assign roles that are equal to or lower than your own hierarchy level.

---

## 🚀 Quick Actions Reference

### From Group Homepage:

-   **"Manage Group"** → Open management panel
-   **"Gateway Access"** → Access group gateway features
-   **"Back to My Groups"** → Return to your groups list

### From Management Panel:

-   **"Add Member"** → Add new users to the group
-   **🗑️ (trash icon)** → Remove a member
-   **Role dropdown** → Change member's role
-   **"Save Changes"** → Save group detail updates
-   **"Back to Group"** → Return to group homepage

---

## ⚠️ Common Scenarios

### Scenario 1: "I can't see Manager Actions"

**Check:**

-   Are you logged in as a manager or admin?
-   Is your role's `hierarchy_level` set correctly? (Should be 50+ for managers)
-   Do you have the `manage_group_members` or `edit_group_description` permission?

### Scenario 2: "I can't assign Manager role to others"

**Reason:** Only administrators (hierarchy level 100) can assign manager roles. Managers can only assign Staff and Member roles.

### Scenario 3: "I can't remove myself"

**Reason:** To prevent groups from having no managers, you cannot remove yourself if you're the only manager or admin in the group.

### Scenario 4: "I can't edit certain member's roles"

**Reason:** You cannot edit roles of users who have a higher hierarchy level than you. For example, managers (level 50) cannot edit administrators (level 100).

---

## 🛠️ Technical Details

### Database Structure:

-   Your manager role is stored in the `roles` table with `hierarchy_level = 50`
-   Group membership is tracked in `group_members` table
-   Permissions are checked via Laravel Gates in `AppServiceProvider`

### Permission Gates Used:

-   `manage-group` → Can edit group details
-   `edit-group-description` → Can update name/description
-   `manage-group-members` → Can add/remove members
-   `edit-member-roles-in-group` → Can change member roles

### Key Files:

-   Livewire Component: `app/Livewire/User/ManageGroup.php`
-   View: `resources/views/livewire/user/manage-group.blade.php`
-   Routes: `routes/web.php` (line ~78-82)
-   Permissions: `app/Providers/AppServiceProvider.php`

---

## 💡 Tips for Managers

1. **Keep descriptions clear** - Help members understand the group's purpose
2. **Assign appropriate roles** - Staff for gateway access, Member for basic access
3. **Review members regularly** - Remove inactive users to keep the group organized
4. **Don't remove yourself** - Always have at least one other manager before leaving
5. **Use the search** - When adding members, use the search to find users quickly

---

## 🆘 Need Help?

If you're having issues:

1. Check the **debug card** on the group homepage (if debugging is enabled)
2. Verify your role hierarchy level
3. Contact a system administrator for assistance
4. Review the Laravel logs at `storage/logs/laravel.log`

---

## ✅ Success Checklist

-   [ ] I can see "Manager Actions" on my group homepage
-   [ ] I can click "Manage Group" and see the management panel
-   [ ] I can edit the group name and description
-   [ ] I can add new members to the group
-   [ ] I can change member roles (Staff/Member)
-   [ ] I can remove members from the group
-   [ ] My role hierarchy is 50 (Manager level)

---

**Last Updated:** November 3, 2025  
**System Version:** Laravel 11 with Group-Specific RBAC
