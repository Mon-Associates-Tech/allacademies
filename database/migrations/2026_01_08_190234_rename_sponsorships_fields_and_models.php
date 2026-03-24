<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('sponsorship_programs', 'sponsorship_projects');
        Schema::rename('sponsor_offers', 'sponsorship_offers');
        Schema::table('sponsorship_beneficiaries', function (Blueprint $table) {
            $table->renameColumn('sponsorship_program_id', 'sponsorship_project_id');
        });
        Schema::table('sponsorship_bids', function (Blueprint $table) {
            $table->renameColumn('sponsorship_program_id', 'sponsorship_project_id');
            $table->renameColumn('sponsor_offer_id', 'sponsorship_offer_id');
        });

        Schema::table('sponsorship_contributions', function (Blueprint $table) {
            $table->renameColumn('sponsorship_program_id', 'sponsorship_project_id');
            $table->renameColumn('sponsor_offer_id', 'sponsorship_offer_id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
