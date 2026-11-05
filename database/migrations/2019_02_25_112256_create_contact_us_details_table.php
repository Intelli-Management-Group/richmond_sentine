<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactUsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_us_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string("email_id")->nullable();
            $table->string("phone")->nullable();
            $table->string("office_days")->nullable();
            $table->string("office_hours")->nullable();
            $table->string("mailing_address")->nullable();
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
        Schema::dropIfExists('contact_us_details');
    }
}
