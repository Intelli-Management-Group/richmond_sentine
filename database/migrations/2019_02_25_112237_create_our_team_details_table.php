<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurTeamDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_team_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name")->nullable();
            $table->string("profile_image")->nullable();
            $table->string("position")->nullable();
            $table->string("twitter_handle")->nullable();
            $table->string("email_id")->nullable();
            $table->text("description")->nullable();
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
        Schema::dropIfExists('our_team_details');
    }
}
