<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserArticleReadDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userwise_article_read_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("userId")->nullable();
            $table->integer("articleId")->nullable();
            $table->dateTime("dateTime")->nullable();
            $table->boolean('readStatus')->default(1)->comment("0-Not read,1-Read");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('userwise_article_read_details');
    }
}
