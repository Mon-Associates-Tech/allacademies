<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('lms_certificate_templates')) {
            Schema::create('lms_certificate_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('type')->default('course'); // course, achievement, participation
                $table->text('description')->nullable();
                $table->string('template_file'); // Blade template name
                $table->json('default_fields')->nullable(); // Default field configuration
                $table->string('background_image')->nullable();
                $table->string('orientation')->default('landscape'); // landscape, portrait
                $table->string('paper_size')->default('a4'); // a4, letter, etc.
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('school_id');
                $table->index('type');
                $table->index('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_certificate_templates');
    }
};
