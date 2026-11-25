<?php

const LONG_HEADING = 'Prime Minister Carney strengthens Canada’s alliance with Europe at the Summit of the European Political Community';

function articleListHtml(int $categoryId): string
{
    return test()->getJson("/articles/{$categoryId}/International", ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->json('html');
}

it('lists articles that are also in the top slider', function () {
    $categoryId = createCategory('International');
    $featuredId = createFeature('Featured');
    $articleId = createArticle(['categoryId' => $categoryId, 'featureId' => (string) $featuredId]);

    $response = $this->get("/articles/{$categoryId}/International")->assertOk();

    expect($response->viewData('top_slider')->pluck('id'))->toContain($articleId)
        ->and($response->viewData('articles')->pluck('id'))->toContain($articleId);
});

it('clamps long card titles so the publish date still shows', function () {
    $categoryId = createCategory('International');
    createArticle([
        'categoryId' => $categoryId,
        'heading' => LONG_HEADING,
        'publishDate' => '2026-11-16 09:00:00',
    ]);

    expect(articleListHtml($categoryId))
        ->toContain('thumbnail-title">'.LONG_HEADING.'</p>')
        ->toContain('November 16, 2026');
});

it('shows only published articles from the category', function () {
    $categoryId = createCategory('International');
    $otherCategoryId = createCategory('Canada');
    createArticle(['categoryId' => $categoryId, 'heading' => 'Published here']);
    createArticle(['categoryId' => $categoryId, 'heading' => 'Unpublished here', 'status' => 0]);
    createArticle(['categoryId' => $otherCategoryId, 'heading' => 'Other category']);

    expect(articleListHtml($categoryId))
        ->toContain('Published here')
        ->not->toContain('Unpublished here')
        ->not->toContain('Other category');
});

it('shows the same video carousel as the home page, YouTube videos included', function () {
    $categoryId = createCategory('International');
    $uploadedId = createVideo(['heading' => 'Uploaded video', 'videoDuration' => '02:15', 'publishDate' => '2026-11-20 10:00:00']);
    $youtubeId = createVideo(['heading' => 'YouTube video', 'isYoutubeVideo' => 1, 'videoFile' => null, 'videoThumbnail' => 'yt.jpg', 'publishDate' => '2026-11-21 10:00:00']);

    $response = $this->get("/articles/{$categoryId}/International")->assertOk();

    expect($response->viewData('videos')->pluck('id')->all())->toBe([$youtubeId, $uploadedId])
        ->and($response->viewData('videos')->pluck('id')->all())->toBe($this->get('/')->viewData('videos')->pluck('id')->all());
    $response->assertSee('href="'.url('/videos', [$youtubeId, 'youtube-video']).'"', false)
        ->assertSee('uploads/video_thumbnail/yt.jpg', false)
        ->assertSee('video-youtube-icon', false)
        ->assertSee('02:15')
        ->assertDontSee('uploads/video/test.mp4', false)
        ->assertDontSee('video-play-pause', false);
});
