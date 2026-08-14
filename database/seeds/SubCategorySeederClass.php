<?php

use Illuminate\Database\Seeder;

class SubCategorySeederClass extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("category_details")->insert(array(
            array(
                "name" => "Community News",
                "isActualCategory" => 1,
                "parentId" => 5,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ),
            array(
                "name" => "Non-Profit",
                "isActualCategory" => 1,
                "parentId" => 5,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ),
            array(
                "name" => "Arts & Cultural",
                "isActualCategory" => 1,
                "parentId" => 5,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ), array(
                "name" => "Sports",
                "isActualCategory" => 1,
                "parentId" => 5,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ), array(
                "name" => "Business",
                "isActualCategory" => 1,
                "parentId" => 5,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ), array(
                "name" => "Crime",
                "isActualCategory" => 1,
                "parentId" => 5,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ), array(
                "name" => "Provincial News",
                "isActualCategory" => 1,
                "parentId" => 6,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ), array(
                "name" => "National",
                "isActualCategory" => 1,
                "parentId" => 6,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ), array(
                "name" => "Latest News",
                "isActualCategory" => 0,
                "parentId" => 5,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ), array(
                "name" => "Latest News",
                "isActualCategory" => 0,
                "parentId" => 6,
                "level" => 1,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 1,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ), array(
                "name" => "All Sports",
                "isActualCategory" => 1,
                "parentId" => 11,
                "level" => 2,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 0,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            ), array(
                "name" => "School Sports",
                "isActualCategory" => 1,
                "parentId" => 11,
                "level" => 2,
                "isDisplayInMore" => 0,
                "isDisplayInMenu" => 0,
                "isDisplayInApp" => 0,
                "isCouponCategory" => 0,
                "isNotificationOn" => 1
            )
        ));
    }
}
