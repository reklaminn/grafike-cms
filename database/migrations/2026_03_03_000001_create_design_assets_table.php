<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_assets', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // css, js, template_css
            $table->string('name');
            $table->longText('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        Schema::create('design_asset_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_asset_id')->constrained()->onDelete('cascade');
            $table->longText('content');
            $table->timestamp('created_at');
        });

        Schema::create('smtp_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('host');
            $table->unsignedSmallInteger('port')->default(587);
            $table->string('encryption', 10)->default('tls'); // tls, ssl, none
            $table->string('username');
            $table->text('password');
            $table->string('from_email');
            $table->string('from_name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Add sitemap fields to seo_entries
        if (Schema::hasTable('seo_entries')) {
            Schema::table('seo_entries', function (Blueprint $table) {
                if (!Schema::hasColumn('seo_entries', 'sitemap_priority')) {
                    $table->decimal('sitemap_priority', 2, 1)->default(0.5)->after('is_noindex');
                }
                if (!Schema::hasColumn('seo_entries', 'sitemap_changefreq')) {
                    $table->string('sitemap_changefreq', 20)->default('weekly')->after('sitemap_priority');
                }
                if (!Schema::hasColumn('seo_entries', 'sitemap_exclude')) {
                    $table->boolean('sitemap_exclude')->default(false)->after('sitemap_changefreq');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('design_asset_backups');
        Schema::dropIfExists('design_assets');
        Schema::dropIfExists('smtp_profiles');

        if (Schema::hasTable('seo_entries')) {
            Schema::table('seo_entries', function (Blueprint $table) {
                $table->dropColumn(['sitemap_priority', 'sitemap_changefreq', 'sitemap_exclude']);
            });
        }
    }
};
