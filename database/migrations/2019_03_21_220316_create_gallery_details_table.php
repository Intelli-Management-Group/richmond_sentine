<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fileName')->nullable();
            $table->tinyInteger('type')->nullable()->comment("1-Image,2-Video");
            $table->string('heading')->nullable();
            $table->string('creditCaption')->nullable();
            $table->integer('authorId')->nullable();
            $table->string('thumbnailImage')->nullable();
            $table->dateTime('publishDate')->nullable();
            $table->boolean('isYoutubeVideo')->nullable();
            $table->boolean('status')->nullable();
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
        Schema::dropIfExists('gallery_details');
    }
}
