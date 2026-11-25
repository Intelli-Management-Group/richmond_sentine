<?php

// Lists show only when an article was published; the detail screen also says when it was last edited.

function editedArticle(): int
{
    return createArticle([
        'publishDate' => '2026-11-10 10:00:00',
        'created_at' => '2026-11-10 10:00:00',
        'updated_at' => '2026-11-20 15:15:00',
    ]);
}

it('sends the publish date, not the edit date, in lists', function () {
    $id = editedArticle();

    $response = test()->withHeader('Authorization', appToken())
        ->post('/api/get-featured-article', ['active_page' => 1, 'total_records' => 10])
        ->assertOk();

    $article = collect($response->json('featured_articles'))->firstWhere('article_id', $id);

    expect($article['publish_date'])->toBe('Nov 10, 2026')
        ->and($article['last_updated'])->toBe('');
});

it('sends both the publish date and the edit date on the detail screen', function () {
    $id = editedArticle();

    $article = test()->withHeader('Authorization', appToken())
        ->get("/api/article-by-id/$id")
        ->assertOk()
        ->json('article_data');

    expect($article['publish_date'])->toBe('Nov 10, 2026')
        ->and($article['last_updated'])->toStartWith('Last Updated: Nov 20, 2026 at 03:15 pm');
});

it('sends no edit date on the detail screen for an article never edited', function () {
    $id = createArticle([
        'publishDate' => '2026-11-10 10:00:00',
        'created_at' => '2026-11-10 10:00:00',
        'updated_at' => '2026-11-10 10:00:00',
    ]);

    $article = test()->withHeader('Authorization', appToken())
        ->get("/api/article-by-id/$id")
        ->assertOk()
        ->json('article_data');

    expect($article['publish_date'])->toBe('Nov 10, 2026')
        ->and($article['last_updated'])->toBe('');
});
