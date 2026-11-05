<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatLangBusinessDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->string('latitude')->nullable()->after('country');
            $table->string('longitude')->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->dropColumn("latitude");
            $table->dropColumn("longitude");
        });
    }
}
