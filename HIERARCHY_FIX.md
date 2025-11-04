# Hierarchy Levels - Correct Configuration

## ✅ Fixed Hierarchy Levels

| Role              | Hierarchy Level | Badge Color      | Can Manage Groups |
| ----------------- | --------------- | ---------------- | ----------------- |
| **Administrator** | 100             | #dc2626 (Red)    | ✅ All groups     |
| **Manager**       | 70              | #f59e0b (Orange) | ✅ Own group only |
| **Staff**         | 30              | #059669 (Green)  | ❌ No             |
| **Member**        | 10              | #6b7280 (Gray)   | ❌ No             |

## 🔍 Verification

To verify the hierarchy levels are correct, run:

```bash
php artisan tinker
```

Then execute:

```php
\App\Models\Role::select('name', 'hierarchy_level', 'badge_color')->get();
```

Expected output:

```
name: administrator, hierarchy_level: 100, badge_color: #dc2626
name: member, hierarchy_level: 10, badge_color: #6b7280
name: staff, hierarchy_level: 30, badge_color: #059669
name: manager, hierarchy_level: 70, badge_color: #f59e0b
```

## 🎯 Policy Check Logic

The `GroupPolicy` checks:

```php
// Manager role has hierarchy_level >= 70
return $membership->role->hierarchy_level >= 70;
```

So:

-   ✅ Manager (70) → Can edit own group
-   ✅ Administrator (100) → Can edit any group
-   ❌ Staff (30) → Cannot edit groups
-   ❌ Member (10) → Cannot edit groups

## 🧪 Test Now

1. **Login as manager@example.com**
2. **Navigate to Gateway Test Group**
3. **Click "Edit Group Details"**
4. **Should work!** ✅

The manager role now has:

-   hierarchy_level = **70** ✅
-   Permissions: `edit_own_group`, `manage_own_group_members` ✅
-   Orange badge color for visual distinction ✅
