<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_user_details', function (Blueprint $table) {
            $table->renameColumn("uniqueId", "deviceId");
            $table->renameColumn("imeiNumber", "deviceToken");
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
            $table->renameColumn("deviceId", "uniqueId");
            $table->renameColumn("deviceToken", "imeiNumber");
        });
    }
}
