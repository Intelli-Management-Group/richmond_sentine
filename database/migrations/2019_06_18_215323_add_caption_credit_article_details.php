<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCaptionCreditArticleDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_file_details', function (Blueprint $table) {
            $table->text("caption")->nullable()->after("isMain");
            $table->text("credit")->nullable()->after("caption");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_file_details', function (Blueprint $table) {
            $table->dropColumn("caption");
            $table->dropColumn("credit");
        });
    }
}
