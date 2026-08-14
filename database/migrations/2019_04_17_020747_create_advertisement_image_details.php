<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisementImageDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisement_file_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("advertisementId")->nullable();
            $table->string('horizontalImage')->nullable();
            $table->string('squareImage')->nullable();
            $table->string('verticalImage')->nullable();
            $table->integer("categoryId")->nullable();
            $table->boolean("isDefault")->default(0)->comment("Is display in all category");
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
        Schema::dropIfExists('advertisement_file_details');
    }
}
