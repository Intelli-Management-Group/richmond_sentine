<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

/*
 * Saving the admin article form (POST /insert-article) must store exactly the features that
 * are ticked. A form with no boxes ticked sends no featureId at all, so that has to clear them.
 */

beforeEach(function () {
    $id = DB::table('users')->insertGetId([
        'user_name' => 'admin',
        'password' => bcrypt('secret'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $this->actingAs(User::find($id));
});

/** Update an existing article through the form, sending $input on top of valid fields. */
function updateArticle(int $articleId, int $categoryId, array $input = [])
{
    $mainFileId = DB::table('article_file_details')->where('articleId', $articleId)->value('id');

    return test()->post('/insert-article', array_merge([
        'article_id' => $articleId,
        'heading' => 'Edited heading',
        'summary' => 'Edited summary',
        'description' => 'Edited description',
        'authorId' => 1,
        'categoryId' => [$categoryId],
        'isMainSelected' => $mainFileId,
    ], $input))->assertOk()->assertJsonStructure(['list']);
}

it('clears the features when the admin unticks them all', function () {
    $categoryId = createCategory('Ontario', createCategory('Canada'));
    $sliderId = createFeature('Slider');
    $featuredId = createFeature('Featured');
    $articleId = createArticle([
        'categoryId' => $categoryId,
        'featureId' => $sliderId.','.$featuredId,
    ]);

    updateArticle($articleId, $categoryId);

    expect(DB::table('article_details')->where('id', $articleId)->value('featureId'))->toBe('');
});

it('stores only the features still ticked', function () {
    $categoryId = createCategory('Ontario', createCategory('Canada'));
    $sliderId = createFeature('Slider');
    $featuredId = createFeature('Featured');
    $articleId = createArticle([
        'categoryId' => $categoryId,
        'featureId' => $sliderId.','.$featuredId,
    ]);

    updateArticle($articleId, $categoryId, ['featureId' => [(string) $featuredId]]);

    expect(DB::table('article_details')->where('id', $articleId)->value('featureId'))->toBe((string) $featuredId);
});
