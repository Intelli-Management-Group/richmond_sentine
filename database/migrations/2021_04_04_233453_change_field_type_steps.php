<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldTypeSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('userwise_steps_details', function (Blueprint $table) {
            $table->integer("totalSteps")->change();
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
            $table->varchar("totalSteps",30)->change();
        });
    }
}
