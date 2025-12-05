<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add third-party app redirect URL for OAuth integration
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('third_party_app_url')->nullable()->after('description');
            $table->string('oauth_client_id')->nullable()->after('third_party_app_url');
            $table->boolean('enable_gateway_redirect')->default(false)->after('oauth_client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['third_party_app_url', 'oauth_client_id', 'enable_gateway_redirect']);
        });
    }
};
