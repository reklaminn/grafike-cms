<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('location', 100)->nullable();
            $table->foreignId('language_id')->constrained('languages');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title', 255);
            $table->string('url', 500)->nullable();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->string('css_class', 255)->nullable();
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('custom_html')->nullable();
            $table->json('json_config')->nullable();
            $table->integer('legacy_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('menu_items')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
