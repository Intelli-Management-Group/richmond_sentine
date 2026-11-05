<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVideosArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_details', function (Blueprint $table) {
            $table->boolean('isVideo')->default(0)->nullable()->after("imageCredit");
            $table->string('videoCredit')->nullable()->after("isVideo");
            $table->string('videoThumbnail')->nullable()->after("videoCredit");
            $table->boolean('isYoutubeVideo')->default(0)->nullable()->after("videoThumbnail");
            $table->string('youtubeUrl')->nullable()->after("isYoutubeVideo");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_details', function (Blueprint $table) {
            $table->dropColumn("isVideo");
            $table->dropColumn("videoCredit");
            $table->dropColumn("videoThumbnail");
            $table->dropColumn("isYoutubeVideo");
            $table->dropColumn("youtubeUrl");
        });
    }
}
