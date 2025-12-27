<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorship_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sponsorship_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['sponsor_offer_id', 'sponsorship_program_id', 'user_id'], 'unique_bid');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorship_bids');
    }
};
