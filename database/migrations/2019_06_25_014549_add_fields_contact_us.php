<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsContactUs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_us_details', function (Blueprint $table) {
            $table->string("place_an_ad_phone")->nullable()->after("phone");
            $table->string("about_us_email")->nullable()->after("email_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_us_details', function (Blueprint $table) {
            $table->dropColumn("place_an_ad_phone");
            $table->dropColumn("about_us_email");
        });
    }
}
