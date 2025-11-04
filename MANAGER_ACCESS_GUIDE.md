# 🎯 Quick Start Guide: Manager Access

## Test Manager Permissions

### 1. Login as Manager

```
Email: manager@example.com
Password: [check your seeder]
```

### 2. Navigate to Your Group

```
Dashboard → My Groups → Gateway Test Group → Gateway Access
```

### 3. Look for This Card:

```
┌─────────────────────────────────────────────────┐
│ 🛡️ Manager Actions                     [YELLOW] │
├─────────────────────────────────────────────────┤
│ ℹ️ Manager Privileges                           │
│ You have manager-level access to this group.   │
│ You can edit group details and manage members. │
│                                                 │
│ [✏️ Edit Group Details]  [👥 Manage Members]   │
└─────────────────────────────────────────────────┘
```

### 4. Click "Edit Group Details"

-   ✅ Should load the edit form
-   ✅ You can change name and description
-   ✅ You can add/remove members

### 5. Try to Edit Another Group

```
Visit: /admin/groups/1/edit (Administrators group)
```

-   ❌ Should show "403 Forbidden" error
-   ❌ You don't have permission

---

## Permission Matrix

| User Type      | Can Edit Own Group   | Can Edit Other Groups | Can Delete Groups |
| -------------- | -------------------- | --------------------- | ----------------- |
| System Admin   | ✅ Yes               | ✅ Yes                | ✅ Yes            |
| Manager        | ✅ Yes (only theirs) | ❌ No                 | ❌ No             |
| Regular Member | ❌ No                | ❌ No                 | ❌ No             |

---

## Visual Differences

### Manager View:

```
┌──────────────────────────┐
│ Group Details           │
│ Group Members           │
│ 🟡 Manager Actions      │  ← Yellow/Warning theme
│ Navigation              │
└──────────────────────────┘
```

### System Admin View:

```
┌──────────────────────────┐
│ Group Details           │
│ Group Members           │
│ 🟡 Manager Actions      │  ← If also a manager
│ 🔴 System Admin Actions │  ← Red/Error theme
│ Navigation              │
└──────────────────────────┘
```

### Regular Member View:

```
┌──────────────────────────┐
│ Group Details           │
│ Group Members           │
│ Navigation              │  ← No action cards!
└──────────────────────────┘
```

---

## Troubleshooting

### ❌ "Manager Actions" card not showing?

**Check 1:** Verify user's role hierarchy

```powershell
php artisan tinker
$user = User::where('email', 'manager@example.com')->first();
$membership = $user->groupMemberships()->first();
$membership->role->hierarchy_level; // Should be >= 70
```

**Check 2:** Verify permissions

```powershell
$user->hasPermission('edit_own_group'); // Should be true
```

**Check 3:** Clear caches

```powershell
php artisan optimize:clear
```

### ❌ Getting 403 Forbidden?

This is **expected behavior** if:

-   You're not a manager of that group
-   You're trying to edit a different group
-   You don't have the required permissions

### ✅ Everything working?

You should see:

1. Manager Actions card on group homepage
2. Ability to edit your own group
3. Cannot edit other groups (403 error)
4. System admins still have full access

---

## Database Check

### Verify Manager Setup:

```sql
SELECT
    u.email,
    g.name as group_name,
    r.name as role_name,
    r.hierarchy_level
FROM users u
JOIN group_members gm ON u.id = gm.user_id
JOIN groups g ON gm.group_id = g.id
JOIN roles r ON gm.role_id = r.id
WHERE u.email = 'manager@example.com';
```

Expected result:

```
| email                   | group_name         | role_name | hierarchy_level |
|------------------------|-------------------|-----------|-----------------|
| manager@example.com    | Gateway Test Group | manager   | 70              |
```

---

**Status**: ✅ Ready to test!
**URL**: http://localhost:8000/dashboard
