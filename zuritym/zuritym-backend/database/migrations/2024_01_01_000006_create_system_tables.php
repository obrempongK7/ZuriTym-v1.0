<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Withdrawal requests
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->string('withdrawal_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->decimal('amount_points', 15, 2);
            $table->decimal('amount_cash', 15, 2)->nullable();
            $table->string('payment_method'); // paypal, mpesa, bank, etc.
            $table->json('payment_details');
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->string('screenshot')->nullable(); // for offline payment proof
            $table->text('admin_note')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->foreign('processed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Push notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('image')->nullable();
            $table->string('click_action')->nullable();
            $table->json('data')->nullable();
            $table->enum('target', ['all', 'specific', 'group'])->default('all');
            $table->json('target_users')->nullable();
            $table->enum('status', ['draft', 'sent', 'failed'])->default('draft');
            $table->integer('sent_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });

        // App settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general');
            $table->string('type')->default('text'); // text, number, boolean, json, file
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['group', 'key']);
        });

        // Ad network configs
        Schema::create('ad_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('config');
            $table->boolean('is_active')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Global chat room
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->timestamps();

            $table->index('created_at');
        });

        // Fraud logs
        Schema::create('fraud_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('event_type');
            $table->string('ip_address')->nullable();
            $table->string('device_id')->nullable();
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->boolean('is_reviewed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'event_type']);
        });

        // Leaderboard (refreshed daily via cron)
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->integer('rank')->default(0);
            $table->decimal('total_points', 15, 2)->default(0);
            $table->decimal('weekly_points', 15, 2)->default(0);
            $table->decimal('monthly_points', 15, 2)->default(0);
            $table->timestamps();

            $table->index('rank');
            $table->index('total_points');
        });

        // Payment methods (configurable)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->decimal('min_withdrawal', 15, 2)->default(0);
            $table->decimal('max_withdrawal', 15, 2)->nullable();
            $table->decimal('conversion_rate', 10, 6)->default(1.000000); // points to cash
            $table->json('fields')->nullable(); // dynamic form fields
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Sessions for sanctum
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('leaderboards');
        Schema::dropIfExists('fraud_logs');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('ad_networks');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('withdrawals');
    }
};
