<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressFieldsBusinessDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->renameColumn("address", "address1");
            $table->string('address2')->nullable()->after('address');
            $table->string('city')->nullable()->after('address2');
            $table->string('province')->nullable()->after('city');
            $table->string('postalCode')->nullable()->after('province');
            $table->string('country')->nullable()->after('postalCode');
        });
        Schema::table('userwise_business_details', function (Blueprint $table) {
            $table->renameColumn("address", "address1");
            $table->string('address2')->nullable()->after('address');
            $table->string('city')->nullable()->after('address2');
            $table->string('province')->nullable()->after('city');
            $table->string('postalCode')->nullable()->after('province');
            $table->string('country')->nullable()->after('postalCode');
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
            $table->renameColumn("address1", "address");
            $table->dropColumn("address2");
            $table->dropColumn("city");
            $table->dropColumn("province");
            $table->dropColumn("postalCode");
            $table->dropColumn("country");
        });
        Schema::table('userwise_business_details', function (Blueprint $table) {
            $table->renameColumn("address1", "address");
            $table->dropColumn("address2");
            $table->dropColumn("city");
            $table->dropColumn("province");
            $table->dropColumn("postalCode");
            $table->dropColumn("country");
        });
    }
}
