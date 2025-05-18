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
            $table->string('description')->fullText();
            $table->json('tags');
            $table->timestamps();
            $table->softDeletes();
        });

//        DB::statement("ALTER TABLE images ADD INDEX images_tags_index((CAST(tags AS CHAR(255) ARRAY)))");
        DB::statement("ALTER TABLE images
ADD COLUMN first_tag VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(tags, '$[0]'))) STORED,
ADD INDEX images_tags_index (first_tag);
");
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
