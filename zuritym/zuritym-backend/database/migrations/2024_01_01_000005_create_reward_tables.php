<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User task completions
        Schema::create('user_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();
            $table->enum('status', ['pending', 'completed', 'rejected', 'flagged'])->default('pending');
            $table->decimal('earned_points', 10, 2)->default(0);
            $table->string('screenshot')->nullable();
            $table->string('proof_url')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('device_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'task_id']);
            $table->index(['user_id', 'status']);
        });

        // Offerwalls
        Schema::create('offerwalls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['api', 'web', 'sdk']);
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('url')->nullable();
            $table->string('postback_secret')->nullable();
            $table->string('icon')->nullable();
            $table->decimal('conversion_rate', 10, 4)->default(1.0000);
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Offerwall completions
        Schema::create('offerwall_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('offerwall_id');
            $table->foreign('offerwall_id')->references('id')->on('offerwalls')->cascadeOnDelete();
            $table->string('offer_id');
            $table->string('offer_name')->nullable();
            $table->decimal('payout', 10, 2)->default(0);
            $table->decimal('points_awarded', 10, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'reversed'])->default('completed');
            $table->string('transaction_id')->nullable()->unique();
            $table->json('postback_data')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Spin wheel segments
        Schema::create('spin_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('points', 10, 2)->default(0);
            $table->enum('type', ['points', 'bonus', 'empty', 'voucher'])->default('points');
            $table->integer('probability')->default(10); // out of 100
            $table->string('color')->default('#FF6B6B');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Spin history
        Schema::create('spin_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('spin_reward_id');
            $table->foreign('spin_reward_id')->references('id')->on('spin_rewards')->cascadeOnDelete();
            $table->decimal('points_won', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // Scratch cards
        Schema::create('scratch_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->decimal('points_won', 10, 2)->default(0);
            $table->boolean('is_scratched')->default(false);
            $table->timestamp('scratched_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_scratched']);
        });

        // Promo codes
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->decimal('reward_points', 10, 2)->default(0);
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->integer('per_user_limit')->default(1);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
        });

        // User promo code usage
        Schema::create('user_promo_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('promo_code_id');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->cascadeOnDelete();
            $table->decimal('points_earned', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'promo_code_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_promo_codes');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('scratch_cards');
        Schema::dropIfExists('spin_histories');
        Schema::dropIfExists('spin_rewards');
        Schema::dropIfExists('offerwall_completions');
        Schema::dropIfExists('offerwalls');
        Schema::dropIfExists('user_tasks');
    }
};
