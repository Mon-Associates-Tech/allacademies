<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('library_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('card_number')->unique();
            $table->string('barcode')->unique();
            $table->enum('card_type', ['student', 'premium'])->default('student');
            $table->enum('status', ['active', 'suspended', 'expired'])->default('active');
            $table->datetime('issued_date');
            $table->datetime('expiry_date');
            $table->datetime('suspended_at')->nullable();
            $table->foreignId('suspended_by')->nullable()->constrained('users');
            $table->datetime('renewed_at')->nullable();
            $table->foreignId('renewed_by')->nullable()->constrained('users');
            $table->foreignId('issued_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['student_id']);
            $table->index(['status']);
            $table->index('expiry_date');
            $table->index('card_number');
            $table->index('barcode');
        });
    }

    public function down()
    {
        Schema::dropIfExists('library_cards');
    }
};
