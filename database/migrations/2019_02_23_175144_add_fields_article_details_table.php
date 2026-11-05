<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsArticleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_details', function (Blueprint $table) {
            $table->text('heading')->nullable()->after("summary");
            $table->string('videoFile')->nullable()->after("imageCredit");
            $table->string('source')->nullable()->after("videoFile");
            $table->text('title')->change();
            $table->text('verticalImage')->change();
            $table->text('squareImage')->change();
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
            $table->dropColumn("heading");
            $table->dropColumn("videoFile");
            $table->dropColumn("source");
            $table->string('title')->change();
            $table->string('verticalImage')->change();
            $table->string('squareImage')->change();
        });
    }
}
