<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAppUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('app_user_details', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->nullable()->comment("1-ios,2-andorid"); 
            $table->string('imeiNumber')->nullable();
            $table->string('uniqueId')->nullable();
            $table->string('latitude',50)->nullable();
            $table->string('longitude',50)->nullable();
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
         Schema::dropIfExists('app_user_details');
    }
}
