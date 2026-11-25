<?php

use App\Services\WebsiteFeed;

function websiteIds($collection): array
{
    return $collection->pluck('id')->unique()->values()->all();
}

function feed(): WebsiteFeed
{
    return app(WebsiteFeed::class);
}

it('picks the same home slider as the website', function () {
    $slider = createFeature('Slider');
    $featured = createFeature('Featured');
    $a = createArticle(['featureId' => (string) $slider, 'publishDate' => '2026-11-20 10:00:00']);
    $b = createArticle(['featureId' => (string) $featured, 'publishDate' => '2026-11-19 10:00:00']);
    $c = createArticle(['featureId' => "$slider,$featured", 'publishDate' => '2026-11-18 10:00:00']);
    createArticle(['publishDate' => '2026-11-21 10:00:00']);

    expect(feed()->homeSliderIds())->toBe([$a, $c])
        ->and(feed()->homeSliderIds())->toBe(websiteIds($this->get('/')->assertOk()->viewData('top_slider')));
});

// HomeController::index has a Featured fallback, but its count((array) $collection) == 0
// check is never true, so the website shows an empty slider. The app mirrors that.
it('picks the same empty home slider as the website when nothing is tagged Slider', function () {
    createFeature('Slider');
    $featured = createFeature('Featured');
    createArticle(['featureId' => (string) $featured]);
    createArticle();

    expect(feed()->homeSliderIds())->toBe([])
        ->and(feed()->homeSliderIds())->toBe(websiteIds($this->get('/')->assertOk()->viewData('top_slider')));
});

it('picks the same latest news as the website ticker', function () {
    $ids = [];
    foreach (range(1, 6) as $day) {
        $ids[$day] = createArticle(['publishDate' => "2026-11-0{$day} 10:00:00"]);
    }
    createVideo(['publishDate' => '2026-11-10 10:00:00']);
    createArticle(['status' => 0, 'publishDate' => '2026-11-10 10:00:00']);

    expect(feed()->latestNewsIds())->toBe([$ids[6], $ids[5], $ids[4], $ids[3], $ids[2]])
        ->and(feed()->latestNewsIds())->toBe(websiteIds($this->get('/')->assertOk()->viewData('latest_news')));
});

it('picks the same home sections as the website', function () {
    $community = createCategory('Community');
    $shown = createCategory('Arts & Culture', $community);
    $hidden = createCategory('Crime', $community, ['isDisplayInFrontend' => 0]);
    $canada = createCategory('Canada');
    $provincial = createCategory('Provincial News', $canada);
    $international = createCategory('International');

    $inShown = createArticle(['categoryId' => $shown]);
    $inHidden = createArticle(['categoryId' => $hidden]);
    $inProvincial = createArticle(['categoryId' => $provincial]);
    $inInternational = createArticle(['categoryId' => $international]);

    $sections = feed()->homeSections();
    $page = $this->get('/')->assertOk();

    expect(array_column($sections, 'category_id'))->toBe([$community, $canada, $international])
        ->and(array_column($sections, 'name'))->toBe(['Community', 'Canada', 'International'])
        ->and($sections[0]['article_ids'])->toBe(websiteIds($page->viewData('community_news')))
        ->and($sections[1]['article_ids'])->toBe(websiteIds($page->viewData('canada_news')))
        ->and($sections[2]['article_ids'])->toBe(websiteIds($page->viewData('international_news')))
        ->and($sections[0]['article_ids'])->toBe([$inShown])
        ->and($sections[1]['article_ids'])->toBe([$inProvincial])
        ->and($sections[2]['article_ids'])->toBe([$inInternational]);
});

it('resolves category ids the way the website category page does', function () {
    $community = createCategory('Community');
    $arts = createCategory('Arts & Culture', $community);
    $sports = createCategory('Sports', $community);
    createCategory('Crime', $community, ['isDisplayInFrontend' => 0]);

    expect(feed()->categoryIdsFor($community))->toBe([$arts, $sports])
        ->and(feed()->categoryIdsFor($arts))->toBe([$arts]);
});

it('picks the same category slider as the website category page', function () {
    $featured = createFeature('Featured');
    $community = createCategory('Community');
    $arts = createCategory('Arts & Culture', $community);
    $sports = createCategory('Sports', $community);
    $f1 = createArticle(['categoryId' => $arts, 'featureId' => (string) $featured, 'publishDate' => '2026-11-20 10:00:00']);
    $f2 = createArticle(['categoryId' => $sports, 'featureId' => (string) $featured, 'publishDate' => '2026-11-21 10:00:00']);
    createArticle(['categoryId' => $sports]);

    $ids = feed()->categorySliderIds(feed()->categoryIdsFor($community));

    expect($ids)->toBe([$f2, $f1])
        ->and($ids)->toBe(websiteIds($this->get("/articles/{$community}/community")->assertOk()->viewData('top_slider')));
});

it('lists category articles the way the website category page does', function () {
    $community = createCategory('Community');
    $arts = createCategory('Arts & Culture', $community);
    $old = createArticle(['categoryId' => $arts, 'publishDate' => '2026-11-01 10:00:00']);
    $new = createArticle(['categoryId' => $arts, 'publishDate' => '2026-11-02 10:00:00']);
    createArticle(['categoryId' => $arts], 0);
    createArticle(['categoryId' => $arts, 'status' => 0]);
    createVideo(['categoryId' => $arts]);

    $ids = feed()->listQuery([$arts], [])->pluck('id')->all();

    expect($ids)->toBe([$new, $old])
        ->and($ids)->toBe(websiteIds($this->get("/articles/{$community}/community")->assertOk()->viewData('articles')))
        ->and(feed()->listQuery([$arts], [$new])->pluck('id')->all())->toBe([$old]);
});

it('leaves out articles without main media', function () {
    $slider = createFeature('Slider');
    $id = createArticle(['featureId' => (string) $slider], 0);

    expect(feed()->homeSliderIds())->not->toContain($id);
});

it('lists an article with two main media rows once', function () {
    $slider = createFeature('Slider');
    $id = createArticle(['featureId' => (string) $slider], 2);

    expect(feed()->homeSliderIds())->toBe([$id])
        ->and(feed()->latestNewsIds())->toBe([$id]);
});

it('loads full articles in the order of the ids given', function () {
    $first = createArticle(['heading' => 'First']);
    $second = createArticle(['heading' => 'Second']);

    $articles = feed()->load([$second, $first]);

    expect($articles->pluck('id')->all())->toBe([$second, $first])
        ->and($articles->first()->heading)->toBe('Second');
});
