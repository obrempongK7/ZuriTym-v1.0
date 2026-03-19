<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('banner')->nullable();
            $table->enum('type', [
                'watch_video', 'visit_website', 'app_install',
                'daily_offer', 'quiz', 'social_follow',
                'survey', 'custom', 'spin', 'scratch'
            ]);
            $table->decimal('reward_points', 10, 2)->default(0);
            $table->string('action_url')->nullable();
            $table->integer('timer_seconds')->default(0);
            $table->integer('completion_limit')->default(1); // times per user
            $table->integer('daily_limit')->default(1);
            $table->integer('total_limit')->nullable(); // overall cap
            $table->integer('completion_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_screenshot')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->json('geo_target')->nullable(); // country codes
            $table->json('requirements')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
