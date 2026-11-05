<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisementDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisement_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('placement')->nullable();
            $table->string('aboveCaption')->nullable();
            $table->string('belowCaption')->nullable();
            $table->text('fileName')->nullable();
            $table->text('link')->nullable();
            $table->string('categoryIds')->nullable();
            $table->boolean('type')->nullable()->comment("1-Regular,2-Premium");
            $table->boolean('imageType')->nullable()->comment("1-Banner,2-Square");
            $table->boolean('status')->nullable()->comment("1-Approved,0-Unapproved");
            $table->datetime("publishDate")->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer("createdBy")->nullable();
            $table->integer("updatedBy")->nullable();
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
        Schema::dropIfExists('advertisement_details');
    }
}
