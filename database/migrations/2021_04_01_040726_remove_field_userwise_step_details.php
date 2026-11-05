<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldUserwiseStepDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('userwise_steps_details', function (Blueprint $table) {
            $table->dropColumn("todaySteps");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('userwise_steps_details', function (Blueprint $table) {
            $table->string("todaySteps",30)->nullable()->after("totalSteps");
        });
    }
}
