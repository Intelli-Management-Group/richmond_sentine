<?php

use Illuminate\Database\Seeder;

class FeatureSeederClass extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("article_feature_details")->insert(array(
            array(
                "name" => "Latest"
            ),
            array(
                "name" => "Popular"
            ),
            array(
                "name" => "Slider"
            ),
            array(
                "name" => "Featured"
            ),
            array(
                "name" => "Trending"
            )
        ));
    }
}
