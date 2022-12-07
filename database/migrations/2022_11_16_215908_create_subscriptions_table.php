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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('package');
            $table->string('reference')->unique();
            $table->integer('beneficiaries')->default(1);
            $table->string('amount');
            $table->string('currency')->default('GHS');
            $table->string('status')->default('unpaid');
            $table->foreignId('team_id')->constrained();
            $table->foreignId('subscriber_id')->constrained('users');
            $table->timestamp('expires_at');
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
        Schema::dropIfExists('subscriptions');
    }
};
