<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('label', 120)->nullable();
            $table->text('html_template')->nullable();
            $table->json('schema_json')->nullable();
            $table->json('default_content_json')->nullable();
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_template_versions');
    }
};
