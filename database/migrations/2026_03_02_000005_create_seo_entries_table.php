<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seoable_id');
            $table->string('seoable_type', 255);
            $table->string('slug', 500)->index();
            $table->foreignId('language_id')->constrained('languages');
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('h1_override', 255)->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->text('hreflang_tags')->nullable();
            $table->boolean('is_noindex')->default(false);
            $table->text('page_css')->nullable();
            $table->text('page_js')->nullable();
            $table->integer('legacy_id')->nullable();
            $table->timestamps();

            $table->index(['seoable_id', 'seoable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_entries');
    }
};
