<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookshop_customers')) {
            Schema::create('bookshop_customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('password');
 
                // Location fields — match the country/region/city select components.
                // Used to resolve which branch fulfills a customer's orders.
                $table->string('country')->nullable();
                $table->string('country_code', 8)->nullable();
                $table->string('region')->nullable();
                $table->string('city')->nullable();
                $table->string('address')->nullable();
 
                $table->foreignId('preferred_branch_id')->nullable()
                ->constrained('bookshop_branches')->nullOnDelete();
 
                $table->boolean('is_active')->default(true);
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
 
                $table->index(['region', 'city']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_customers');
    }
};
