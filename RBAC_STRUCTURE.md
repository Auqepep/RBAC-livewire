# RBAC Structure Documentation

## 🏢 Group-Based Role Assignment System

This system implements a hierarchical RBAC structure where:

### Structure

```
GROUPS (Departments/Teams)
├── IT Support
├── Marketing
├── Human Resources
└── System Administrators

ROLES (Positions within Groups)
├── Super Admin (Level 100)
├── Admin (Level 90)
├── Manager (Level 70)
├── Staff (Level 50)

USERS
└── Assigned to ROLES within specific GROUPS
```

### How It Works

1. **Groups** represent departments, teams, or organizational units
2. **Roles** represent positions or job functions that can exist across groups
3. **Users** are assigned specific roles within specific groups
4. **Permissions** are attached to roles (not directly to users)

### Database Structure

-   `groups` table: Stores department/team information
-   `roles` table: Stores role definitions and hierarchy levels
-   `permissions` table: Stores available permissions
-   `group_members` table: Links users to roles within specific groups
-   `role_permissions` table: Links roles to their permissions

### Key Relationships

```sql
group_members table:
- user_id (which user)
- group_id (in which group/department)
- role_id (with what role/position)
- assigned_by (who assigned them)
- joined_at (when they joined)
```

### Example Usage

```php
// Assign John as IT Manager in IT Support department
GroupMember::assignUserToGroupRole(
    userId: $john->id,
    groupId: $itSupportGroup->id,
    roleId: $itManagerRole->id
);

// Assign Sarah as Marketing Specialist in Marketing department
GroupMember::assignUserToGroupRole(
    userId: $sarah->id,
    groupId: $marketingGroup->id,
    roleId: $marketingSpecialistRole->id
);
```

### Benefits

1. **Flexible**: Users can have different roles in different groups
2. **Hierarchical**: Roles have levels for permission inheritance
3. **Scalable**: Easy to add new departments and roles
4. **Secure**: Permissions controlled through role assignments
5. **Auditable**: Track who assigned roles and when

### Current Available Groups

-   System Administrators
-   IT Support
-   Marketing
-   Human Resources

### Current Available Roles

-   Super Admin, Admin (System-wide)
-   IT Manager, IT Staff (IT-focused)
-   Marketing Manager, Marketing Specialist (Marketing-focused)
-   HR Manager (HR-focused)
-   Manager, Staff, Member (General roles)
