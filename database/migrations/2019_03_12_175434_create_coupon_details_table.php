<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('heading')->nullable();
            $table->integer('categoryId')->nullable();
            $table->longText('highlights')->nullable();
            $table->longText('finePrints')->nullable();
            $table->longText('detail')->nullable();
            $table->string('companyName')->nullable();
            $table->string('companyPhone', 50)->nullable();
            $table->string('companyEmail', 100)->nullable();
            $table->string('companyAddress')->nullable();
            $table->text('bannerImage')->nullable();
            $table->text('thumbnailImage')->nullable();
            $table->decimal('originalPrice', 10, 2)->nullable();
            $table->decimal('discountPrice', 10, 2)->nullable();
            $table->dateTime('publishDate')->nullable();
            $table->string('offerDetail')->nullable();
            $table->string('daywiseTime')->nullable();
            $table->boolean('status')->default(0)->comment("0-Unpublished,1-Published");
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
        Schema::dropIfExists('coupon_details');
    }
}
