<?php

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Tests\Concerns\RefreshTestDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createCategory(string $name, ?int $parentId = null): int
{
    return DB::table('category_details')->insertGetId([
        'name' => $name,
        'parentId' => $parentId,
        'level' => $parentId ? 1 : 0,
        'isDisplayInFrontend' => 1,
        'isCouponCategory' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function createFeature(string $name): int
{
    return DB::table('article_feature_details')->insertGetId([
        'name' => $name,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

/**
 * A published article with the main image the listing queries join on.
 */
function createArticle(array $attributes = []): int
{
    $id = DB::table('article_details')->insertGetId(array_merge([
        'heading' => 'Test article',
        'status' => 1,
        'isVideo' => 0,
        'featureId' => '',
        'publishDate' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ], $attributes));

    DB::table('article_file_details')->insert([
        'articleId' => $id,
        'fileName' => 'test.jpg',
        'isMain' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $id;
}

function createVideo(array $attributes = []): int
{
    return DB::table('article_details')->insertGetId(array_merge([
        'heading' => 'Test video',
        'status' => 1,
        'isVideo' => 1,
        'isYoutubeVideo' => 0,
        'videoFile' => 'test.mp4',
        'videoThumbnail' => 'test.jpg',
        'publishDate' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ], $attributes));
}
