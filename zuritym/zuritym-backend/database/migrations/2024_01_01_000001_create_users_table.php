<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable()->default('default_avatar.png');
            $table->string('google_id')->nullable()->unique();
            $table->string('referral_code')->unique()->nullable();
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->foreign('referred_by')->references('id')->on('users')->nullOnDelete();

            // Fraud prevention
            $table->string('device_id')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->string('last_ip')->nullable();
            $table->string('registration_ip')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->text('block_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->integer('fraud_score')->default(0);
            $table->boolean('is_verified')->default(false);

            // Status & role
            $table->enum('status', ['active', 'inactive', 'blocked', 'pending'])->default('active');
            $table->enum('role', ['user', 'admin'])->default('user');

            // Stats
            $table->integer('total_referrals')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('country')->nullable();
            $table->string('timezone')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['email', 'status']);
            $table->index('referral_code');
            $table->index('device_id');
            $table->index('registration_ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
