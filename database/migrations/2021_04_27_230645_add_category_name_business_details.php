<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryNameBusinessDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->string('otherCategoryName')->nullable()->after('shortCode');
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
            $table->dropColumn('otherCategoryName');
        });
    }
}
