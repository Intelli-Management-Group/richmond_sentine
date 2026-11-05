<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCategoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_category_notification_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("userId")->nullable();
            $table->integer("categoryId")->nullable();
            $table->boolean('isNotificationOn')->default(1)->comment("0-Off,1-On");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_category_notification_details');
    }
}
