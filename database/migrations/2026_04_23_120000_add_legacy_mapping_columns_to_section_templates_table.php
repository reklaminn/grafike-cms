<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('section_templates', function (Blueprint $table) {
            $table->string('legacy_module_key', 255)->nullable()->after('component_key');
            $table->json('legacy_config_map_json')->nullable()->after('schema_json');
        });
    }

    public function down(): void
    {
        Schema::table('section_templates', function (Blueprint $table) {
            $table->dropColumn(['legacy_module_key', 'legacy_config_map_json']);
        });
    }
};
