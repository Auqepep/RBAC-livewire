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
        Schema::table('group_roles', function (Blueprint $table) {
            // Make group_id nullable to support global role templates
            $table->dropForeign(['group_id']);
            $table->dropUnique(['group_id', 'name']);
            $table->unsignedBigInteger('group_id')->nullable()->change();
            
            // Add is_template column to distinguish templates from group-specific roles
            $table->boolean('is_template')->default(false);
            
            // Make created_by nullable for system templates
            $table->dropForeign(['created_by']);
            $table->unsignedBigInteger('created_by')->nullable()->change();
            
            // Add back foreign keys with nullable support
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // Add new unique constraint for templates (name must be unique for templates)
            $table->unique(['name', 'is_template']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_roles', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['created_by']);
            $table->dropUnique(['name', 'is_template']);
            $table->dropColumn('is_template');
            
            $table->unsignedBigInteger('group_id')->nullable(false)->change();
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
            
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['group_id', 'name']);
        });
    }
};
