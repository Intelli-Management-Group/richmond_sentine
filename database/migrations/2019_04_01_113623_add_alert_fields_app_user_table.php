<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlertFieldsAppUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_user_details', function (Blueprint $table) {
            $table->boolean('notificationAlertStatus')->default(0)->after("allow_location_detection");
            $table->boolean('soundStatus')->default(0)->after("notificationAlertStatus");
            $table->boolean('vibrateStatus')->default(0)->after("soundStatus");
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
