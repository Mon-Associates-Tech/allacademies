<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookshop_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();

            // Location fields — match the country/region/city select components
            $table->string('country')->nullable();
            $table->string('country_code', 8)->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);

            // Nullable + set null on delete: a branch shouldn't vanish because
            // the staff member who created it was later removed.
            $table->foreignId('created_by_staff_id')->nullable()
                ->constrained('bookshop_staff')->nullOnDelete();

            $table->timestamps();

            $table->index(['region', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_branches');
    }
};
