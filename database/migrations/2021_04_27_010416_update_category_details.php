<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCategoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_categories', function (Blueprint $table) {
            $table->tinyInteger('parentId')->nullable()->after('name');
            $table->tinyInteger('level')->nullable()->after('parentId');
            $table->boolean('isActualCategory')->default(0)->after('isActive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_categories', function (Blueprint $table) {
            $table->dropColumn('parentId');
            $table->dropColumn('level');
            $table->dropColumn('isActualCategory');
        });
    }
}
