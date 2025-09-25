<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('category')->default('general');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Groups table - organizational units (IT Support, HR, Finance, etc.)
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Roles table - generic roles (Manager, Supervisor, Staff, etc.)
        // NOT tied to specific groups - can be used in any group
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // manager, supervisor, staff, admin
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('badge_color')->default('#6366f1');
            $table->integer('hierarchy_level')->default(0); // 0 = lowest, higher = more senior
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Role permissions - what each role can do (global permissions for roles)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });

        // Group members - users belong to groups with assigned roles
        // This is where the magic happens: user + group + role combination
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('joined_at')->default(now());
            $table->timestamps();
            
            // User can only have one role per group
            $table->unique(['group_id', 'user_id']);
            
            $table->index(['user_id', 'role_id']);
            $table->index(['group_id', 'role_id']);
        });

        // Group-specific role permissions (optional: additional permissions for roles within specific groups)
        Schema::create('group_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->foreignId('granted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['group_id', 'role_id', 'permission_id']);
        });

        // Group join requests
        Schema::create('group_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('requested_role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('message')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index(['group_id', 'user_id', 'status']);
        });

        // Email OTPs table
        Schema::create('email_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('otp');
            $table->enum('type', ['login', 'verification'])->default('login');
            $table->boolean('is_used')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['email', 'otp', 'type']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_otps');
        Schema::dropIfExists('group_join_requests');
        Schema::dropIfExists('group_role_permissions');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('permissions');
    }
};
