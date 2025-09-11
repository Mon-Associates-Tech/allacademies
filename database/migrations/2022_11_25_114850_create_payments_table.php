<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('amount');
            $table->string('currency')->default('GHS');
            $table->string('status')->default('pending');
            // $table->foreignId('subscription_id')->nullable()->constrained();
              // Regular subscriptions
    $table->foreignId('subscription_id')->nullable()->constrained();

    // Book subscriptions
    $table->foreignId('book_subscription_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
