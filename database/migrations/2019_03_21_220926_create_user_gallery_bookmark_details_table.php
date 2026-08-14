<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGalleryBookmarkDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_gallery_bookmark_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("userId")->nullable();
            $table->integer("galleryId")->nullable();
            $table->dateTime("dateTime")->nullable();
            $table->boolean('bookmarkStatus')->default(0)->comment("0-Not bookmarked,1-Bookmarked");
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
        Schema::dropIfExists('user_gallery_bookmark_details');
    }
}
