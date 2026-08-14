<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexCategoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_details', function (Blueprint $table) {
            $table->dropColumn("isNotificationOn");
            $table->tinyInteger("nav_menu_index")->default(0)->nullable()->comment("Index for top navigation")->after("level");
            $table->tinyInteger("more_section_index")->default(0)->nullable()->comment("Index for more menu")->after("nav_menu_index");
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
            $table->boolean('isNotificationOn')->default(1)->comment("0-Off,1-On")->after("isCouponCategory");
            $table->dropColumn("nav_menu_index");
            $table->dropColumn("more_section_index");
        });
    }
}
