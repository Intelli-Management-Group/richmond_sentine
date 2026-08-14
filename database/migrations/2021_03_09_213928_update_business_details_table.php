<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBusinessDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->dropColumn('ownerName');
            $table->dropColumn('ownerPhone');
            $table->dropColumn('ownerEmail');
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
            $table->string('ownerName')->nullable();
            $table->string('ownerPhone')->nullable();
            $table->string('ownerEmail')->nullable();
        });
    }
}
