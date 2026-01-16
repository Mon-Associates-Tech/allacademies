<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('description');
            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText('description');
            }
            $table->json('tags');
            $table->timestamps();
            $table->softDeletes();
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE images
ADD COLUMN first_tag VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(tags, '$[0]'))) STORED,
ADD INDEX images_tags_index (first_tag);
");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
};
