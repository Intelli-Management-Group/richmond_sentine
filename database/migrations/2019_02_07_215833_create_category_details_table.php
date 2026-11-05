<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('iconImage')->nullable();
            $table->boolean('isActualCategory')->default(0);
            $table->integer("parentId")->nullable();
            $table->integer("level")->nullable();
            $table->boolean('isDisplayInMore')->default(1)->comment("0-Not allow,1-Allow");
            $table->boolean('isDisplayInMenu')->default(1)->comment("0-Not allow,1-Allow");
            $table->boolean('isDisplayInApp')->default(1)->comment("0-Not allow,1-Allow");
            $table->boolean('isCouponCategory')->default(1)->comment("0-Off,1-On");
            $table->boolean('isNotificationOn')->default(1)->comment("0-Off,1-On");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_details');
    }
}
