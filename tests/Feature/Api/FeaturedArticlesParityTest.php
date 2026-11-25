<?php

use App\Services\WebsiteFeed;

function homeFeed(array $params = []): \Illuminate\Testing\TestResponse
{
    return test()->withHeader('Authorization', appToken())
        ->post('/api/get-featured-article', array_merge(['active_page' => 1, 'total_records' => 10], $params))
        ->assertOk()
        ->assertJson(['ResponseCode' => '1']);
}

function articleIds(array $items): array
{
    return array_map(fn ($item) => (int) $item['article_id'], $items);
}

it('puts the website home slider in top_five_articles', function () {
    $slider = createFeature('Slider');
    $featured = createFeature('Featured');
    $a = createArticle(['featureId' => (string) $slider, 'publishDate' => '2026-11-20 10:00:00']);
    createArticle(['featureId' => (string) $featured, 'publishDate' => '2026-11-19 10:00:00']);
    $c = createArticle(['featureId' => "$slider,$featured", 'publishDate' => '2026-11-18 10:00:00']);

    expect(articleIds(homeFeed()->json('top_five_articles')))->toBe([$a, $c])
        ->and(articleIds(homeFeed(['top_article_count' => 1])->json('top_five_articles')))->toBe([$a]);
});

it('defaults the slider to 5 for app builds that send no top_article_count', function () {
    $slider = createFeature('Slider');
    foreach (range(1, 9) as $day) {
        createArticle(['featureId' => (string) $slider, 'publishDate' => "2026-11-0{$day} 10:00:00"]);
    }

    expect(homeFeed()->json('top_five_articles'))->toHaveCount(5)
        ->and(homeFeed(['top_article_count' => 8])->json('top_five_articles'))->toHaveCount(8);
});

it('keeps slider articles in featured_articles', function () {
    $slider = createFeature('Slider');
    $featured = createFeature('Featured');
    $b = createArticle(['featureId' => (string) $featured, 'publishDate' => '2026-11-19 10:00:00']);
    $c = createArticle(['featureId' => "$slider,$featured", 'publishDate' => '2026-11-18 10:00:00']);

    $response = homeFeed();

    expect(articleIds($response->json('top_five_articles')))->toBe([$c])
        ->and(articleIds($response->json('featured_articles')))->toContain($b)->toContain($c);
});

it('lists every published article in featured_articles, newest first', function () {
    $featured = createFeature('Featured');
    $old = createArticle(['featureId' => (string) $featured, 'publishDate' => '2026-11-10 10:00:00']);
    $new = createArticle(['featureId' => '', 'publishDate' => '2026-11-12 10:00:00']);
    $mid = createArticle(['featureId' => '', 'publishDate' => '2026-11-11 10:00:00']);
    $video = createArticle(['isVideo' => 1, 'publishDate' => '2026-11-13 10:00:00']);
    $draft = createArticle(['status' => 0, 'publishDate' => '2026-11-14 10:00:00']);

    $response = homeFeed();
    $ids = array_values(array_intersect(articleIds($response->json('featured_articles')), [$old, $new, $mid, $video, $draft]));

    expect($ids)->toBe([$new, $mid, $old])
        ->and($response->json('total_records'))->toBe(3);
});

it('sends no home extras unless the app asks for them', function () {
    createCategory('Community');
    createArticle();

    $response = homeFeed();

    expect($response->json('latest_news'))->toBe([])
        ->and($response->json('home_sections'))->toBe([]);
});

it('adds the website latest news and home sections on page 1 when asked', function () {
    $community = createCategory('Community');
    $arts = createCategory('Arts & Culture', $community);
    $canada = createCategory('Canada');
    $provincial = createCategory('Provincial News', $canada);
    $international = createCategory('International');
    createArticle(['categoryId' => $arts]);
    createArticle(['categoryId' => $provincial]);
    createArticle(['categoryId' => $international]);

    $response = homeFeed(['include_home' => 1]);
    $feed = app(WebsiteFeed::class);

    expect(articleIds($response->json('latest_news')))->toBe($feed->latestNewsIds())
        ->and(array_column($response->json('home_sections'), 'category_id'))->toBe([$community, $canada, $international])
        ->and(array_column($response->json('home_sections'), 'name'))->toBe(['Community', 'Canada', 'International'])
        ->and(array_map(fn ($s) => articleIds($s['articles']), $response->json('home_sections')))
        ->toBe(array_column($feed->homeSections(), 'article_ids'));
});

it('sends no home extras or slider on page 2, but lists slider articles there in date order', function () {
    $slider = createFeature('Slider');
    $featured = createFeature('Featured');
    foreach (range(10, 24) as $day) {
        // The two oldest are the slider; by date they belong on page 2.
        $features = $day <= 11 ? "$slider,$featured" : (string) $featured;
        createArticle(['featureId' => $features, 'publishDate' => "2026-11-{$day} 10:00:00"]);
    }

    $response = homeFeed(['active_page' => 2, 'include_home' => 1]);
    $ids = articleIds($response->json('featured_articles'));
    $slider_ids = app(WebsiteFeed::class)->homeSliderIds();

    expect($response->json('latest_news'))->toBe([])
        ->and($response->json('home_sections'))->toBe([])
        ->and($response->json('top_five_articles'))->toBe([])
        ->and(array_values(array_intersect($ids, $slider_ids)))->toEqualCanonicalizing($slider_ids);
});

it('keeps the existing response keys', function () {
    createArticle(['featureId' => (string) createFeature('Featured')]);

    homeFeed()->assertJsonStructure([
        'total_records', 'top_five_articles', 'featured_articles', 'advertisement_ids',
        'advertisement_data' => ['square', 'banner'],
    ]);
});
