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
        Schema::table('email_otps', function (Blueprint $table) {
            $table->index(['expires_at']);
            $table->index(['is_used']);
            $table->index(['email', 'type', 'is_used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_otps', function (Blueprint $table) {
            $table->dropIndex(['expires_at']);
            $table->dropIndex(['is_used']);
            $table->dropIndex(['email', 'type', 'is_used']);
        });
    }
};
