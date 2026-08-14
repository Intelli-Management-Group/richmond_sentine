<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_user_details', function (Blueprint $table) {
            $table->string('name')->nullable()->after("id");
            $table->string('email')->nullable()->after("name");
            $table->string('avatar')->nullable()->after("email");
            $table->string('phoneNo',30)->nullable()->after("avatar");
            $table->boolean('isVerified')->default(0)->after("deviceId");
            $table->integer('verificationCode')->nullable()->after("isVerified");
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
            $table->dropColumn("name");
            $table->dropColumn("email");
            $table->dropColumn("avatar");
            $table->dropColumn("phoneNo");
            $table->dropColumn("isVerified");
            $table->dropColumn("verificationCode");
        });
    }
}
