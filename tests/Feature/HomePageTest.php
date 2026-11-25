<?php

it('shows both uploaded and YouTube videos in the video section', function () {
    $uploadedId = createVideo(['heading' => 'Uploaded video']);
    $youtubeId = createVideo(['heading' => 'YouTube video', 'isYoutubeVideo' => 1, 'videoFile' => null]);
    $unpublishedId = createVideo(['heading' => 'Unpublished video', 'isYoutubeVideo' => 1, 'status' => 0]);

    $videoIds = $this->get('/')->assertOk()->viewData('videos')->pluck('id');

    expect($videoIds)->toContain($uploadedId)
        ->toContain($youtubeId)
        ->not->toContain($unpublishedId);
});

it('links video carousel cards to the video page instead of playing inline', function () {
    $youtubeId = createVideo(['heading' => 'YouTube video', 'isYoutubeVideo' => 1, 'videoFile' => null, 'videoThumbnail' => 'yt.jpg']);

    $this->get('/')
        ->assertOk()
        ->assertSee('href="'.url('/videos', [$youtubeId, 'youtube-video']).'"', false)
        ->assertSee('uploads/video_thumbnail/yt.jpg', false)
        ->assertDontSee('<video', false)
        ->assertDontSee('video-play-pause', false);
});

it('marks YouTube cards with the YouTube icon and uploaded cards with their duration', function () {
    createVideo(['heading' => 'Uploaded video', 'videoDuration' => '02:15']);

    $this->get('/')->assertOk()->assertSee('02:15')->assertDontSee('video-youtube-icon', false);

    createVideo(['heading' => 'YouTube video', 'isYoutubeVideo' => 1, 'videoFile' => null]);

    $this->get('/')->assertOk()->assertSee('video-youtube-icon', false);
});

it('clamps long card titles so the publish date still shows', function () {
    $categoryId = createCategory('International');
    $heading = 'Prime Minister Carney strengthens Canada’s alliance with Europe at the Summit of the European Political Community';
    createArticle(['categoryId' => $categoryId, 'heading' => $heading, 'publishDate' => '2026-11-16 09:00:00']);

    $this->get('/')
        ->assertOk()
        ->assertSee('thumbnail-title">'.str_limit($heading, 100).'</p>', false)
        ->assertSee('November 16, 2026');
});
