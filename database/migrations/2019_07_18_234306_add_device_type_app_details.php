<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeviceTypeAppDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_user_details', function (Blueprint $table) {
            $table->tinyInteger('device_type')->nullable()->comment("1-mobile,2-tablet/ipad")->after("type"); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_user_details', function (Blueprint $table) {
            $table->dropColumn("device_type");
        });
    }
}
