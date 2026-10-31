<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_exam_sync_runs', function (Blueprint $table) {
            $table->id();
            $table->enum('direction', ['pull', 'push']);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->enum('status', ['running', 'success', 'failed'])->default('running');
            $table->unsignedInteger('items_total')->default(0);
            $table->unsignedInteger('items_ok')->default(0);
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['direction', 'status']);
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_exam_sync_runs');
    }
};
