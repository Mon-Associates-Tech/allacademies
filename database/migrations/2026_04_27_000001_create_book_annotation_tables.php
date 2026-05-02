<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_annotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('page_number');
            $table->decimal('x_pct', 7, 4);
            $table->decimal('y_pct', 7, 4);
            $table->decimal('width_pct', 7, 4);
            $table->decimal('height_pct', 7, 4);
            $table->string('color', 20)->default('#f59e0b');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['book_id', 'page_number']);
            $table->index(['school_id', 'book_id']);
        });

        Schema::create('book_annotation_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('book_annotation_id')->constrained('book_annotations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('book_annotation_comments')->cascadeOnDelete();
            $table->text('message');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->index(['book_annotation_id', 'created_at']);
            $table->index(['school_id', 'book_annotation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_annotation_comments');
        Schema::dropIfExists('book_annotations');
    }
};
