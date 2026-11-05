<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBusinessDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->renameColumn('description', 'about');
            $table->longText('description')->nullable()->change();
            $table->string('email')->nullable()->after('description');
            $table->string('logo')->nullable()->after('name');
            $table->text('address')->nullable()->after('logo');
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
            $table->renameColumn("about", "description");
            $table->text("about")->nullable()->change();
            $table->dropColumn("email");
            $table->dropColumn("logo");
            $table->dropColumn("address");
        });
    }
}
