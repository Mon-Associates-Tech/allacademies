<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('subscription_token')->unique();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('source')->default('website'); // website, contact_form, etc.
            $table->json('preferences')->nullable(); // for future use (topics, frequency, etc.)
            $table->timestamps();

            $table->index(['email', 'is_active']);
            $table->index(['subscription_token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscriptions');
    }
};
