<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStepsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('steps_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("userId")->nullable();
            $table->date("stepDate")->nullable();
            $table->integer("totalSteps")->nullable();
            $table->text("hourwiseSteps")->nullable();
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
        Schema::dropIfExists('steps_details');
    }
}
