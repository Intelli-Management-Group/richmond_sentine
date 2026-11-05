<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEditionNumberInEditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("digital_edition_details", function (Blueprint $table) {
            $table->string("editionNumber")->nullable()->after("volumeEdition");
            $table->dropColumn("month");
            $table->dropColumn("year");
            $table->date("editionDate")->nullable()->after("thumbnailImage");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("digital_edition_details", function (Blueprint $table) {
            $table->dropColumn("editionNumber");
            $table->integer("month")->nullable();
            $table->integer("year")->nullable();
            $table->dropColumn("editionDate");
         });
    }
}
