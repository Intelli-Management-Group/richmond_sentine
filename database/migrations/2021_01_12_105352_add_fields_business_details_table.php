<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsBusinessDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->string('ownerName')->nullable()->after('isFeatured');
            $table->string('ownerPhone')->nullable()->after('ownerName');
            $table->string('ownerEmail')->nullable()->after('ownerPhone');
            $table->integer('userId')->nullable()->after('ownerEmail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->dropColumn("ownerName");
            $table->dropColumn("ownerPhone");
            $table->dropColumn("ownerEmail");
            $table->dropColumn("userId");
        });
    }
}
