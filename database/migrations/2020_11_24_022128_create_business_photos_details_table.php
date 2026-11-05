<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessPhotosDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_photos_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('businessId')->nullable();
            $table->string('type',10)->nullable();
            $table->string('fileName')->nullable();
            $table->string('originalName')->nullable();
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
        Schema::dropIfExists('business_photos_details');
    }
}
