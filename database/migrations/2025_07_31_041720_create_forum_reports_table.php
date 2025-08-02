<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id');
            $table->unsignedBigInteger('reporter_id');
            $table->string('reason'); // 'spam', 'inappropriate', 'harassment', 'off_topic', etc.
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['reportable_type', 'reportable_id']);
            $table->index(['status', 'created_at']);
            $table->index('reporter_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_reports');
    }
};
