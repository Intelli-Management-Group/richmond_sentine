<?php

function categoryFeed(int $categoryId, array $params = []): \Illuminate\Testing\TestResponse
{
    return test()->withHeader('Authorization', appToken())
        ->post('/api/categorywise-article', array_merge(['category_id' => $categoryId, 'active_page' => 1, 'total_records' => 10], $params))
        ->assertOk()
        ->assertJson(['ResponseCode' => '1']);
}

function feedIds(array $items): array
{
    return array_map(fn ($item) => (int) $item['article_id'], $items);
}

function communityWithArticles(): array
{
    $featured = createFeature('Featured');
    $community = createCategory('Community');
    $arts = createCategory('Arts & Culture', $community);
    $sports = createCategory('Sports', $community);
    $ids = [
        'featuredArts' => createArticle(['categoryId' => $arts, 'featureId' => (string) $featured, 'publishDate' => '2026-11-20 10:00:00']),
        'featuredSports' => createArticle(['categoryId' => $sports, 'featureId' => (string) $featured, 'publishDate' => '2026-11-19 10:00:00']),
        'arts' => createArticle(['categoryId' => $arts, 'publishDate' => '2026-11-21 10:00:00']),
        'sports' => createArticle(['categoryId' => $sports, 'publishDate' => '2026-11-18 10:00:00']),
    ];

    return [$community, $arts, $ids];
}

it('returns the website category page for a parent category', function () {
    [$community] = communityWithArticles();

    $response = categoryFeed($community);
    $page = $this->get("/articles/{$community}/community")->assertOk();
    $list = feedIds($response->json('article_data'));

    expect(feedIds($response->json('top_five_articles')))->toBe($page->viewData('top_slider')->pluck('id')->unique()->values()->all())
        ->and($list)->not->toBeEmpty()
        ->and($list)->toBe(array_slice($page->viewData('articles')->pluck('id')->all(), 0, count($list)));
});

it('lists slider articles too, as the website category page does', function () {
    [$community, , $ids] = communityWithArticles();

    $list = feedIds(categoryFeed($community)->json('article_data'));

    expect($list)->toBe([$ids['arts'], $ids['featuredArts'], $ids['featuredSports'], $ids['sports']]);
});

it('uses only the category itself when it has no subcategories', function () {
    [, $arts, $ids] = communityWithArticles();

    expect(feedIds(categoryFeed($arts)->json('article_data')))->toBe([$ids['arts'], $ids['featuredArts']]);
});

it('has an empty slider and a full list when nothing in the category is Featured', function () {
    createFeature('Featured');
    $international = createCategory('International');
    createArticle(['categoryId' => $international, 'publishDate' => '2026-11-20 10:00:00']);
    createArticle(['categoryId' => $international, 'publishDate' => '2026-11-21 10:00:00']);

    $response = categoryFeed($international);

    expect($response->json('top_five_articles'))->toBe([])
        ->and($response->json('article_data'))->toHaveCount(2)
        ->and($response->json('total_records'))->toBe(2);
});

it('finds nothing in a category hidden from the app', function () {
    $hidden = createCategory('Hidden', null, ['isDisplayInApp' => 0]);
    createArticle(['categoryId' => $hidden]);

    categoryFeed($hidden)->assertJson(['ResponseText' => 'No article(s) found.', 'article_data' => []]);
});

it('leaves out articles without main media', function () {
    $international = createCategory('International');
    $withMedia = createArticle(['categoryId' => $international]);
    createArticle(['categoryId' => $international], 0);

    expect(feedIds(categoryFeed($international)->json('article_data')))->toBe([$withMedia]);
});
