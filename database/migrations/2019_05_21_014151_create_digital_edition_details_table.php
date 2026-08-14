<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDigitalEditionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digital_edition_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->dateTime('publishDate')->nullable();
            $table->string('volumeEdition')->nullable();
            $table->text('pdfFile')->nullable();
            $table->text('thumbnailImage')->nullable();
            $table->integer("month")->nullable();
            $table->integer("year")->nullable();
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
        Schema::dropIfExists('digital_edition_details');
    }
}
