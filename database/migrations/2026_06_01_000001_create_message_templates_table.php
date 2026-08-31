<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->index();
            $table->string('name');
            $table->string('category')->default('general'); // fee, event, general, reminder
            $table->string('subject');
            $table->longText('body'); // email/in-app body with {{placeholders}}
            $table->text('sms_body')->nullable(); // short SMS version
            $table->json('available_variables')->nullable(); // list of supported {{vars}}
            $table->boolean('is_system')->default(false); // system templates can't be deleted
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
