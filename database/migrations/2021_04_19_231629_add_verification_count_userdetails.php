<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerificationCountUserdetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_user_details', function (Blueprint $table) {
            $table->tinyInteger('verificationCount')->default(1)->after("isVerified");
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
            $table->dropColumn("verificationCount");
        });
    }
}
