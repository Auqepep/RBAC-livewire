<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Complete RBAC structure with:
     * - OTP-based authentication (NO PASSWORDS)
     * - Redis caching for permissions (no cache/jobs tables needed)
     * - OAuth 2.0 support via Laravel Passport
     * - Group-centric role management
     */
    public function up(): void
    {
        // Users table - NO PASSWORDS (OTP Authentication)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_super_admin')->default(false);
            $table->timestamps();
        });

        // Sessions table (database driver for OTP authentication flow)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Email OTPs for authentication (NO PASSWORDS!)
        Schema::create('email_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->boolean('verified')->default(false);
            $table->timestamps();
            
            $table->index(['email', 'otp', 'expires_at']);
        });

        // Groups table
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('third_party_app_url')->nullable();
            $table->string('oauth_client_id')->nullable();
            $table->boolean('enable_gateway_redirect')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Permissions table (global permissions catalog)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
        });

        // Roles table - Group-specific roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->string('badge_color')->default('#6366f1');
            $table->integer('hierarchy_level')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['name', 'group_id'], 'roles_name_group_unique');
        });

        // Role permissions - What each role can do
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });

        // Group members - Users belong to groups with assigned roles
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            
            $table->unique(['group_id', 'user_id']);
            $table->index(['user_id', 'group_id']);
            $table->index(['group_id', 'role_id']);
        });

        // Group join requests
        Schema::create('group_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('message')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['group_id', 'user_id', 'status']);
        });

        // OAuth 2.0 Tables (Laravel Passport)
        // Note: These are created by passport:install, included here for completeness
        // oauth_clients, oauth_access_tokens, oauth_refresh_tokens, 
        // oauth_auth_codes, oauth_device_codes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_join_requests');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('email_otps');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
