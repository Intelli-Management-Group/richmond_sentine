<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleMediaDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_file_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('articleId')->nullable();
            $table->string('type',10)->nullable();
            $table->string('fileName')->nullable();
            $table->string('duration')->nullable();
            $table->string('thumbnailImage')->nullable();
            $table->boolean('isMain')->default(0)->nullable();           
            $table->timestamps();
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_file_details');
    }
}
