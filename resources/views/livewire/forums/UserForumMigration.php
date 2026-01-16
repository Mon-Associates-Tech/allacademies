
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('forum_posts_count')->default(0)->after('email_verified_at');
            $table->unsignedInteger('forum_topics_count')->default(0)->after('forum_posts_count');
            $table->unsignedInteger('forum_reputation')->default(0)->after('forum_topics_count');
            $table->timestamp('last_forum_activity')->nullable()->after('forum_reputation');
            $table->json('forum_preferences')->nullable()->after('last_forum_activity');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'forum_posts_count',
                'forum_topics_count',
                'forum_reputation',
                'last_forum_activity',
                'forum_preferences',
            ]);
        });
    }
};
