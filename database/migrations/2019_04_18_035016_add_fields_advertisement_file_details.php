<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsAdvertisementFileDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertisement_file_details', function (Blueprint $table) {
            $table->tinyInteger("isDisplayInSearch")->default(0)->nullable()->after("isDefault");
            $table->tinyInteger("isDisplayInBookmark")->default(0)->nullable()->after("isDisplayInSearch");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advertisement_file_details', function (Blueprint $table) {
            $table->dropColumn("isDisplayInSearch");
            $table->dropColumn("isDisplayInBookmark");
        });
    }
}
