<?php

it('clamps long video card titles so the publish date still shows', function () {
    $currentId = createVideo(['heading' => 'Video being watched']);
    $heading = 'A long video title that wraps onto more lines than the card has room for on narrow screens';
    createVideo(['heading' => $heading, 'publishDate' => '2026-11-16 09:00:00']);

    $html = $this->getJson("/videos/{$currentId}/video-being-watched", ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->json('html');

    expect($html)
        ->toContain('thumbnail-title">'.str_limit($heading, 80).'</p>')
        ->toContain('November 16, 2026');
});
