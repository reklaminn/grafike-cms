<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->longText('body')->nullable();
            $table->text('excerpt')->nullable();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages');
            $table->unsignedBigInteger('parent_article_id')->nullable()->comment('Translation link');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('sort_order')->default(0);
            $table->string('slug', 500);
            $table->string('external_url', 500)->nullable();
            $table->enum('link_target', ['_self', '_blank'])->default('_self');
            $table->text('template')->nullable();
            $table->unsignedTinyInteger('content_type_id')->default(0);
            $table->unsignedBigInteger('form_id')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->text('meta_description')->nullable();
            $table->text('extra_info')->nullable();
            $table->datetime('published_at')->nullable();
            $table->string('display_date', 100)->nullable();
            $table->foreignId('author_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();
            $table->integer('legacy_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_article_id')->references('id')->on('articles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
