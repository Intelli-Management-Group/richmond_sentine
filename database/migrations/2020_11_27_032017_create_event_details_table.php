<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('eventDate')->nullable();
            $table->string('eventTime')->nullable();
            $table->string('venue')->nullable();
            $table->string('venueAddress')->nullable();
            $table->string('organizerName')->nullable();
            $table->string('organizerPhone')->nullable();
            $table->string('organizerEmail')->nullable();
            $table->string('organizerWebsite')->nullable();
            $table->string('bannerImage')->nullable();
            $table->string('bookingLink')->nullable();
            $table->string('price',10)->nullable();
            $table->integer("businessId")->nullable();
            $table->integer("categoryId")->nullable();
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
        Schema::dropIfExists('event_details');
    }
}
