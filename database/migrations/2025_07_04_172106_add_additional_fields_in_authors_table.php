<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('pen_name')->nullable()->after('name');
            $table->string('website')->nullable()->after('pen_name');
            $table->json('social_links')->nullable()->after('website');
            $table->text('writing_experience')->nullable()->after('biography');
            $table->text('education')->nullable()->after('writing_experience');
            $table->text('awards')->nullable()->after('education');
            $table->text('author_statement')->nullable()->after('awards');
        });
    }

    public function down()
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn([
                'pen_name',
                'website',
                'social_links',
                'writing_experience',
                'education',
                'awards',
                'author_statement'
            ]);
        });
    }
};
