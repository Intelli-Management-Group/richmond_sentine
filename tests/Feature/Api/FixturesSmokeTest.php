<?php

it('authenticates an app user against the test database', function () {
    $this->withHeader('Authorization', appToken())
        ->post('/api/get-featured-article', ['active_page' => 1, 'total_records' => 10])
        ->assertOk()
        ->assertJson(['ResponseCode' => '1', 'ResponseText' => 'No article(s) found.']);
});

it('can give an article several main media rows', function () {
    $id = createArticle([], 2);

    expect(DB::table('article_file_details')->where('articleId', $id)->where('isMain', 1)->count())->toBe(2);
});

it('can create a category hidden from the app', function () {
    $id = createCategory('Hidden', null, ['isDisplayInApp' => 0]);

    expect(DB::table('category_details')->where('id', $id)->value('isDisplayInApp'))->toBe(0);
});
