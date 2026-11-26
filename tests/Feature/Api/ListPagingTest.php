<?php

use Illuminate\Support\Facades\DB;

/**
 * Pages through an app list the way the app does: 10 rows a page, sending back
 * the advertisement ids each page returns. Returns the story ids in the order received.
 */
function pageThrough(string $uri, string $listKey, int $pages, array $params = [], ?string $token = null): array
{
    $token ??= appToken();
    $ids = [];
    $adIds = [];
    foreach (range(1, $pages) as $page) {
        $response = test()->withHeader('Authorization', $token)
            ->post($uri, array_merge(['active_page' => $page, 'total_records' => 10, 'ad_ids' => implode(',', $adIds)], $params))
            ->assertOk()
            ->assertJson(['ResponseCode' => '1']);
        $adIds = $response->json('advertisement_ids');
        foreach ($response->json($listKey) as $item) {
            if (empty($item['is_advertisement'])) {
                $ids[] = (int) $item['article_id'];
            }
        }
    }

    return $ids;
}

/**
 * Makes $count stories one day apart and returns their ids newest first.
 * 30 stories span 4 app pages: each page keeps its every-9th row for an ad.
 */
function storiesNewestFirst(int $count, callable $create): array
{
    return array_map(
        fn ($daysAgo) => $create(['publishDate' => now()->subDays($daysAgo)->format('Y-m-d H:i:s')]),
        range(1, $count)
    );
}

it('sends every story in a category once, in order, across page breaks', function () {
    $international = createCategory('International');
    $stories = storiesNewestFirst(30, fn ($date) => createArticle(['categoryId' => $international] + $date));

    expect(pageThrough('/api/categorywise-article', 'article_data', 4, ['category_id' => $international]))->toBe($stories);
});

it('sends every home story once, in order, across page breaks', function () {
    $stories = storiesNewestFirst(30, fn ($date) => createArticle($date));

    expect(pageThrough('/api/get-featured-article', 'featured_articles', 4))->toBe($stories);
});

it('sends every search result once, in order, across page breaks', function () {
    $stories = storiesNewestFirst(30, fn ($date) => createArticle(['heading' => 'Paging story'] + $date));

    expect(pageThrough('/api/search-data', 'article_data', 4, ['search_keyword' => 'Paging story']))->toBe($stories);
});

it('sends every bookmarked story once, in order, across page breaks', function () {
    $token = appToken();
    $userId = json_decode(base64_decode(base64_decode($token)))->user_id;
    $stories = storiesNewestFirst(30, fn ($date) => createArticle($date));
    foreach ($stories as $id) {
        DB::table('userwise_article_bookmark_details')->insert(['userId' => $userId, 'articleId' => $id, 'bookmarkStatus' => 1]);
    }

    expect(pageThrough('/api/get-article-bookmark-status', 'user_bookmarked_articles', 4, [], $token))->toBe($stories);
});

it('sends every video once, in order, across page breaks', function () {
    $videos = storiesNewestFirst(30, fn ($date) => createVideo($date));

    expect(pageThrough('/api/get-videos', 'video_data', 4))->toBe($videos);
});

it('sends every other video once, in order, across page breaks under a video', function () {
    $playing = createVideo(['publishDate' => now()->format('Y-m-d H:i:s')]);
    $videos = storiesNewestFirst(30, fn ($date) => createVideo($date));

    expect(pageThrough('/api/video-by-id', 'video_data', 4, ['video_id' => $playing]))->toBe($videos);
});
