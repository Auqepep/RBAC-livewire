# Permission Testing System Guide

## Overview

A real-time permission testing system that allows you to test RBAC (Role-Based Access Control) changes instantly without page reloads.

## Quick Start

### 1. Access Points

-   **Permission Test Page**: `/test/permissions` (available to all authenticated users)
-   **Admin Users Page**: `/admin/users` (admin only)
-   **Admin Permissions Page**: `/admin/permissions` (admin only)

### 2. Navigation Links

-   **Admin Layout**: "🧪 Test Permissions" link in top navigation
-   **User Layout**: "🧪 Test Permissions" link in top navigation
-   **Quick access buttons** in admin panels

## How to Test Permissions

### Method 1: Quick Admin Toggle (Easiest)

1. **As Admin**: Go to `/admin/users`
2. **Find a User**: Locate any user (except yourself)
3. **Toggle Admin**: Click the shield icon to grant/remove admin privileges
    - 🛡️ Green = Make Admin
    - ⚠️ Yellow = Remove Admin
4. **Test Immediately**:
    - Click the eye icon next to the user to open their permission test
    - Or open `/test/permissions` in another tab
5. **See Changes**: Permission changes reflect immediately

### Method 2: Role/Group Management

1. **As Admin**: Use `/admin/groups` or `/admin/roles`
2. **Modify Memberships**: Add/remove users from groups
3. **Change Roles**: Assign different roles to users
4. **Test**: Open `/test/permissions` to verify changes

### Method 3: Direct Permission Management

1. **As Admin**: Go to `/admin/permissions`
2. **Modify Permissions**: Create, edit, or deactivate permissions
3. **Test**: Check how changes affect user access

## Testing Features

### Real-time Permission Checker

-   **Live Testing**: Test individual permissions with "Test Now" buttons
-   **Bulk Refresh**: "Refresh" button updates all permissions at once
-   **Auto-refresh**: Toggle automatic updates every 10 seconds
-   **Visual Indicators**:
    -   ✅ Green = Permission Allowed
    -   ❌ Red = Permission Denied

### Permission Categories

The system tests permissions across these categories:

-   **System**: Core system administration
-   **Users**: User management capabilities
-   **Groups**: Group management permissions
-   **Content**: Content creation/editing rights

### Admin Quick Actions

From the admin users page (`/admin/users`):

-   **Shield Icons**: Instant admin privilege toggle
-   **Eye Icons**: Direct link to permission testing
-   **Status Badges**: Visual admin/verified status

## Example Workflow

1. **Login as Admin** → Go to `/admin/users`
2. **Open Test Page** → Click "🧪 Open Permission Test Page" (opens in new tab)
3. **Pick a User** → Find a regular user in the admin panel
4. **Make Admin** → Click the green shield to grant admin privileges
5. **Refresh Test** → Go back to test page, click "Refresh"
6. **See Changes** → User now has admin permissions (shown in green)
7. **Remove Admin** → Go back to admin panel, click yellow shield
8. **Test Again** → Refresh test page to see permissions removed

## Advanced Features

### Auto-refresh Mode

-   Enable automatic testing every 10 seconds
-   Perfect for continuous testing during development
-   Shows real-time notifications for each update

### Multi-tab Testing

-   Keep admin panel open in one tab
-   Keep permission test page open in another
-   Make changes in admin tab, see results in test tab instantly

### Permission Debugging

-   Each permission shows its internal name (e.g., `manage_system`)
-   Color-coded results for quick identification
-   Timestamp tracking for when tests were last run

## Technical Details

### Routes Created

-   `GET /test/permissions` → Permission test dashboard
-   `POST /test/permission` → AJAX endpoint for testing individual permissions
-   `POST /admin/users/{user}/toggle-admin` → Quick admin privilege toggle

### API Endpoints

-   **Test Single Permission**: `POST /test/permission`
    ```json
    { "permission": "manage_system" }
    ```
-   **Response Format**:
    ```json
    {
        "permission": "manage_system",
        "allowed": true,
        "user_id": 123,
        "user_name": "John Doe",
        "timestamp": "2025-10-30T15:30:45.000000Z"
    }
    ```

## Troubleshooting

### Permission Not Updating

1. Clear Laravel caches: `php artisan cache:clear`
2. Check if user is in correct groups
3. Verify role assignments
4. Ensure permissions are active

### Auto-refresh Not Working

1. Check browser console for JavaScript errors
2. Verify CSRF token is present
3. Check network connectivity

### Admin Toggle Not Working

1. Verify you're not trying to modify your own account
2. Check if administrator role exists
3. Ensure proper middleware is applied

## Security Notes

-   Admin toggles only work on other users (not yourself)
-   All permission checks use Laravel's built-in authorization
-   CSRF protection on all state-changing operations
-   Real-time testing doesn't bypass security measures
