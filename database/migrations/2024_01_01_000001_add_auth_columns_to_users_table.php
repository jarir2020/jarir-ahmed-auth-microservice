<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('email_verification_token', 64)->nullable()->after('email_verified_at');
            $table->timestamp('email_verification_sent_at')->nullable()->after('email_verification_token');
            $table->boolean('two_factor_enabled')->default(false)->after('password');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->timestamp('last_online_at')->nullable()->after('two_factor_secret');
            $table->boolean('is_banned')->default(false)->after('last_online_at');
            $table->string('ban_reason')->nullable()->after('is_banned');
            $table->json('notification_preferences')->nullable()->after('ban_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_verified_at',
                'email_verification_token',
                'email_verification_sent_at',
                'two_factor_enabled',
                'two_factor_secret',
                'last_online_at',
                'is_banned',
                'ban_reason',
                'notification_preferences',
            ]);
        });
    }
};
