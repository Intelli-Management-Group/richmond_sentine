<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFrontendIndexCategoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_details', function (Blueprint $table) {
            $table->tinyInteger("frontend_menu_index")->default(0)->nullable()->comment("Index for frontend top navigation")->after("isDisplayInFrontend");
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
            $table->dropColumn("frontend_menu_index");
        });
    }
}
