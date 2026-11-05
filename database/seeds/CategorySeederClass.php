<?php

use Illuminate\Database\Seeder;

class CategorySeederClass extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("category_details")->insert(array(array(
            "name" => "Top Stories",
            "isActualCategory" => 0,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 0,
            "isNotificationOn" => 1
        ), array(
            "name" => "Social Media",
            "isActualCategory" => 0,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 0,
            "isNotificationOn" => 1
        ), array(
            "name" => "Coupon Zone",
            "isActualCategory" => 0,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 0,
            "isNotificationOn" => 1
        ), array(
            "name" => "Book An Ad",
            "isActualCategory" => 0,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 0,
            "isNotificationOn" => 1
        ), array(
            "name" => "Community",
            "isActualCategory" => 1,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 0,
            "isNotificationOn" => 1
        ), array(
            "name" => "Canada",
            "isActualCategory" => 1,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 0,
            "isNotificationOn" => 1
        ), array(
            "name" => "International",
            "isActualCategory" => 1,
            "parentId" => 0,
            "level" => 0,
            "isDisplayInMore" => 0,
            "isDisplayInMenu" => 0,
            "isDisplayInApp" => 1,
            "isCouponCategory" => 0,
            "isNotificationOn" => 1
        )));
    }
}
