<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFieldsCategoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_details', function (Blueprint $table) {
            $table->boolean('isDisplayInFrontend')->default(1)->comment("0-Not allow,1-Allow")->after("isDisplayInApp");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_details', function (Blueprint $table) {
            $table->dropColumn("isDisplayInFrontend");
        });
    }
}
