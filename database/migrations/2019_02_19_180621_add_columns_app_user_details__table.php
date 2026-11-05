<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsAppUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_user_details', function (Blueprint $table) {
            $table->boolean('allow_all_notifications')->default(1)->after("deviceId");
            $table->boolean('allow_location_detection')->default(0)->after("allow_all_notifications");
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
            $table->dropColumn("allow_all_notifications");
            $table->dropColumn("allow_location_detection");
        });
    }
}
