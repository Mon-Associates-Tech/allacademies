<?php

use App\Enums\TeamStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
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
        Schema::table('teams', function (Blueprint $table) {
            $table->json('meta')->default(new Expression('(JSON_OBJECT())'));
            $table->string('status')->default(TeamStatus::DECLINED->value)->index();
            $table->text('declined_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['meta', 'status', 'declined_reason']);
        });
    }
};
