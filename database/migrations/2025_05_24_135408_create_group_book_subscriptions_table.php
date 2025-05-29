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
        Schema::create('group_book_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->string('status'); // active, expired, cancelled
            $table->string('subscribed_by_type');
            $table->unsignedBigInteger('subscribed_by_id');
            $table->timestamps();

            $table->index(['subscribed_by_type']);
            $table->index(['subscribed_by_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_book_subscriptions');
    }
};
