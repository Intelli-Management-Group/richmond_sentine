<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// itereateArticleData reads authorDetail, articleFileDetail and
// categoryDetail.parentCategoryDetail for every article. With lazy loading
// turned off, any list that does not eager-load them fails with a 500.

beforeEach(fn () => Model::preventLazyLoading());
afterEach(fn () => Model::preventLazyLoading(false));

function articlesWithRelations(array $attributes, int $count = 3): array
{
    $author = DB::table('author_details')->insertGetId(['name' => 'Test author', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]);
    $ids = [];
    foreach (range(1, $count) as $day) {
        $ids[] = createArticle(array_merge(['authorId' => $author, 'publishDate' => "2026-11-0{$day} 10:00:00"], $attributes));
    }

    return $ids;
}

it('eager-loads the category article list', function () {
    $community = createCategory('Community');
    $arts = createCategory('Arts & Culture', $community);
    articlesWithRelations(['categoryId' => $arts]);

    $response = test()->withHeader('Authorization', appToken())
        ->post('/api/categorywise-article', ['category_id' => $community, 'active_page' => 1, 'total_records' => 10])
        ->assertOk();

    expect($response->json('article_data'))->toHaveCount(3)
        ->and($response->json('article_data.0.parent_id'))->toBe($community);
});

it('eager-loads the home featured list', function () {
    // One Slider article, so the Featured ones are not pulled into the slider fallback.
    createArticle(['featureId' => (string) createFeature('Slider')]);
    $featured = createFeature('Featured');
    $community = createCategory('Community');
    $arts = createCategory('Arts & Culture', $community);
    articlesWithRelations(['categoryId' => $arts, 'featureId' => (string) $featured]);

    $response = test()->withHeader('Authorization', appToken())
        ->post('/api/get-featured-article', ['active_page' => 1, 'total_records' => 10])
        ->assertOk();

    expect(collect($response->json('featured_articles'))->whereNotNull('article_id'))->toHaveCount(3)
        ->and($response->json('featured_articles.0.author_name'))->toBe('Test author');
});
