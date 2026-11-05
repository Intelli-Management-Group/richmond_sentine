<?php

use Illuminate\Database\Seeder;

class CoupanCategorySeederClass extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("category_details")->insert(array(array(
            "name" => "All",
            "isActualCategory" => 1,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 1,
            "isNotificationOn" => 0
        ), array(
            "name" => "Food & Drinks",
            "isActualCategory" => 1,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 1,
            "isNotificationOn" => 0
        ), array(
            "name" => "Spa & Beauty",
            "isActualCategory" => 1,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 1,
            "isNotificationOn" => 0
        ), array(
            "name" => "Health & Fitness",
            "isActualCategory" => 1,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 1,
            "isNotificationOn" => 0
        ), array(
            "name" => "Events & Activites",
            "isActualCategory" => 1,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 1,
            "isNotificationOn" => 0
        ), array(
            "name" => "Services & Retail",
            "isActualCategory" => 1,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 1,
            "isNotificationOn" => 0
        )));
    }
}
