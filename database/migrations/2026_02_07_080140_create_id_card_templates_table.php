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
        if (! Schema::hasTable('id_card_templates')) {
            Schema::create('id_card_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('template_file'); // Blade template name (professional, modern, academic)
                $table->json('default_fields')->nullable(); // Default field configuration
                $table->json('required_fields')->nullable(); // Fields that must always be present
                $table->string('preview_image')->nullable();
                $table->string('orientation')->default('portrait'); // portrait, landscape
                $table->string('card_size')->default('standard'); // standard (85.6mm x 53.98mm), custom
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('id_card_templates');
    }
};
