<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->nullOnDelete();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->nullOnDelete();
            $table->foreignId('page_template_id')->nullable()->after('template')->constrained('page_templates')->nullOnDelete();
            $table->string('frontend_variant', 100)->nullable()->after('page_template');
            $table->json('sections_json')->nullable()->after('layout_json');
            $table->longText('custom_css')->nullable()->after('sections_json');
            $table->longText('custom_js')->nullable()->after('custom_css');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->nullOnDelete();
            $table->string('listing_variant', 100)->nullable()->after('template');
            $table->string('detail_variant', 100)->nullable()->after('listing_variant');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->nullOnDelete();
            $table->string('theme_variant', 100)->nullable()->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('site_id');
            $table->dropColumn('theme_variant');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('site_id');
            $table->dropColumn(['listing_variant', 'detail_variant']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('site_id');
            $table->dropConstrainedForeignId('page_template_id');
            $table->dropColumn(['frontend_variant', 'sections_json', 'custom_css', 'custom_js']);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('site_id');
        });
    }
};
