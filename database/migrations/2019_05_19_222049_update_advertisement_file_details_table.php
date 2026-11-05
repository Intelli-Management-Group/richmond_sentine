<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAdvertisementFileDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertisement_file_details', function (Blueprint $table) {
            $table->dropColumn("horizontalImage");
            $table->dropColumn("squareImage");
            $table->dropColumn("verticalImage");
            $table->dropColumn("isDefault");
            $table->dropColumn("isDisplayInSearch");
            $table->dropColumn("isDisplayInBookmark");
            $table->string('advertisementFor')->nullable()->after("advertisementId");
            $table->string('position')->nullable()->after("advertisementFor");
            $table->text('imageName')->nullable()->after("position");
            $table->string('categoryId')->nullable()->change();
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
            $table->string('horizontalImage')->nullable()->after("advertisementId");
            $table->string('squareImage')->nullable()->after("horizontalImage");
            $table->string('verticalImage')->nullable()->after("squareImage");
            $table->boolean("isDefault")->default(0)->comment("Is display in all category")->after("categoryId");
            $table->tinyInteger("isDisplayInSearch")->default(0)->nullable()->after("isDefault");
            $table->tinyInteger("isDisplayInBookmark")->default(0)->nullable()->after("isDisplayInSearch");
            $table->dropColumn("advertisementFor");
            $table->dropColumn("position");
            $table->dropColumn("imageName");
            $table->integer("categoryId")->change();
        });
    }
}
