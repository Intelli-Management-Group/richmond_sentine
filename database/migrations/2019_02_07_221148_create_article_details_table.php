<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('summary')->nullable();
            $table->integer("featureId")->nullable();
            $table->integer("authorId")->nullable();
            $table->integer("categoryId")->nullable();
            $table->integer("subCategoryId")->nullable();
            $table->datetime("publishDate")->nullable();
            $table->datetime("expiryDate")->nullable();
            $table->string('verticalImage')->nullable();
            $table->string('squareImage')->nullable();
            $table->text('imageCaption')->nullable();
            $table->string('imageCredit')->nullable();
            $table->string('neighberhood')->nullable();
            $table->text('metaTag')->nullable();
            $table->boolean('status')->default(1)->comment("0-Unpublished,1-Published");
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
        Schema::dropIfExists('article_details');
    }
}
