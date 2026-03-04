<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_url', 500)->index();
            $table->string('to_url', 500);
            $table->integer('status_code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hit_count')->default(0);
            $table->datetime('last_hit_at')->nullable();
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->unique();
            $table->text('value')->nullable();
            $table->string('group', 100)->default('general');
            $table->string('type', 50)->default('text');
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages');
            $table->string('group', 100);
            $table->string('key', 255);
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['language_id', 'group', 'key']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reviewable_id');
            $table->string('reviewable_type', 255);
            $table->string('author_name', 255);
            $table->string('author_email', 255)->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('title', 500)->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->foreignId('language_id')->nullable()->constrained('languages');
            $table->integer('legacy_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['reviewable_id', 'reviewable_type']);
        });

        Schema::create('template_snippets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->longText('content')->nullable();
            $table->string('category', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 10)->unique();
            $table->string('symbol', 10);
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('member_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('phone', 50)->nullable();
            $table->foreignId('group_id')->nullable()->constrained('member_groups')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->datetime('email_verified_at')->nullable();
            $table->rememberToken();
            $table->foreignId('language_id')->nullable()->constrained('languages');
            $table->integer('legacy_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('legacy_id_map', function (Blueprint $table) {
            $table->id();
            $table->string('legacy_table', 100);
            $table->unsignedInteger('legacy_id');
            $table->string('new_table', 100);
            $table->unsignedBigInteger('new_id');
            $table->timestamps();
            $table->unique(['legacy_table', 'legacy_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_id_map');
        Schema::dropIfExists('members');
        Schema::dropIfExists('member_groups');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('template_snippets');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('redirects');
    }
};
