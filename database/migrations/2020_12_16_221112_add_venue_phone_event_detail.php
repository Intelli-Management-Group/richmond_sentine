<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVenuePhoneEventDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_details', function (Blueprint $table) {
            $table->string('venuePhone')->after("venueAddress")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_details', function (Blueprint $table) {
            $table->dropColumn('venuePhone');
        });
    }
}
