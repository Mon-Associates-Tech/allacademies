<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('amount_offered', 12, 2)->default(0);
            $table->text('criteria')->nullable();
            $table->string('status')->default('open'); // open, closed, fulfilled
            $table->boolean('accepts_bids')->default(true);
            $table->date('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_offers');
    }
};
