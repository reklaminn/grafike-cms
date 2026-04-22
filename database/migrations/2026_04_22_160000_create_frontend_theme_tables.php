<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('engine', 100)->default('nextjs-basic-html');
            $table->text('description')->nullable();
            $table->json('assets_json')->nullable();
            $table->json('tokens_json')->nullable();
            $table->json('settings_schema_json')->nullable();
            $table->string('preview_image', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('section_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained('themes')->cascadeOnDelete();
            $table->string('type', 100);
            $table->string('variation', 100);
            $table->string('name', 255);
            $table->enum('render_mode', ['html', 'component'])->default('html');
            $table->string('component_key', 255)->nullable();
            $table->longText('html_template')->nullable();
            $table->json('schema_json')->nullable();
            $table->json('default_content_json')->nullable();
            $table->string('preview_image', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['theme_id', 'type', 'variation'], 'section_templates_theme_type_variation_unique');
        });

        Schema::create('page_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained('themes')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('page_type', 100)->nullable();
            $table->json('sections_json')->nullable();
            $table->json('default_settings_json')->nullable();
            $table->string('preview_image', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('site_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->nullable()->constrained('themes')->nullOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->json('snapshot_json')->nullable();
            $table->string('preview_image', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('domain', 255)->nullable()->unique();
            $table->foreignId('theme_id')->nullable()->constrained('themes')->nullOnDelete();
            $table->foreignId('site_template_id')->nullable()->constrained('site_templates')->nullOnDelete();
            $table->json('tokens_json')->nullable();
            $table->json('settings_json')->nullable();
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
        Schema::dropIfExists('site_templates');
        Schema::dropIfExists('page_templates');
        Schema::dropIfExists('section_templates');
        Schema::dropIfExists('themes');
    }
};
