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

it('clamps long card titles so the publish date still shows', function () {
    $categoryId = createCategory('International');
    $heading = 'Prime Minister Carney strengthens Canada’s alliance with Europe at the Summit of the European Political Community';
    createArticle(['categoryId' => $categoryId, 'heading' => $heading, 'publishDate' => '2026-11-16 09:00:00']);

    $this->get('/')
        ->assertOk()
        ->assertSee('thumbnail-title">'.str_limit($heading, 100).'</p>', false)
        ->assertSee('November 16, 2026');
});
