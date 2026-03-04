<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreignId('language_id')->constrained('languages');
            $table->unsignedBigInteger('root_page_id')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('show_in_menu')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('slug', 500);
            $table->string('external_url', 500)->nullable();
            $table->enum('link_target', ['_self', '_blank'])->default('_self');
            $table->string('module_type', 50)->nullable();
            $table->string('template', 100)->nullable();
            $table->json('layout_json')->nullable();
            $table->text('page_template')->nullable();
            $table->boolean('is_password_protected')->default(false);
            $table->string('page_password', 255)->nullable();
            $table->boolean('show_social_share')->default(false);
            $table->boolean('show_facebook_comments')->default(false);
            $table->boolean('show_breadcrumb')->default(true);
            $table->unsignedInteger('view_count')->default(0);
            $table->integer('legacy_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('pages')->onDelete('set null');
            $table->unique(['slug', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
