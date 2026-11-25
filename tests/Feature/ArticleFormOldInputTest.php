<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

/*
 * The admin add/edit article form (resources/views/article/add.blade.php) must keep what the
 * admin typed after a failed save, and show the saved values otherwise.
 *
 * Only the video form does a real POST that redirects back with input; the article form saves
 * over AJAX and gets JSON errors. So the video tests go through the real submit, and the
 * article-only fields (features, category) are tested with flashed old input.
 */

function loginAdmin(): void
{
    $id = DB::table('users')->insertGetId([
        'user_name' => 'admin',
        'password' => bcrypt('secret'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    test()->actingAs(User::find($id));
}

function createAuthor(string $name): int
{
    return DB::table('author_details')->insertGetId([
        'name' => $name,
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

/** A failed video save (heading left empty) from $page, following the redirect back. */
function submitVideoWithoutHeading(string $page, array $input)
{
    return test()->followingRedirects()
        ->from($page)
        ->post('/insert-video', array_merge(['is_video' => 1, 'heading' => ''], $input))
        ->assertOk()
        ->assertSee('Please enter heading');
}

beforeEach(function () {
    loginAdmin();
});

it('keeps the chosen author and typed text when adding a video fails validation', function () {
    createAuthor('Alice');
    $bobId = createAuthor('Bob');

    submitVideoWithoutHeading('/add-article/1', [
        'authorId' => $bobId,
        'summary' => 'Typed summary',
        'description' => 'Typed description',
        'youtubeUrl' => 'abc123',
        'youtube_duration' => '3:15',
        'videoCredit' => 'Typed credit',
        'viewCount' => '42',
        'metaTag' => 'typed-tag',
    ])
        ->assertSee('value="'.$bobId.'" selected', false)
        ->assertSee('value="Typed summary"', false)
        ->assertSee('>Typed description</textarea>', false)
        ->assertSee('value="abc123"', false)
        ->assertSee('value="3:15"', false)
        ->assertSee('value="Typed credit"', false)
        ->assertSee('value="42"', false)
        ->assertSee('value="typed-tag"', false);
});

it('keeps the new values, not the saved ones, when editing a video fails validation', function () {
    $savedAuthorId = createAuthor('Alice');
    $newAuthorId = createAuthor('Bob');
    $videoId = createVideo([
        'authorId' => $savedAuthorId,
        'summary' => 'Saved summary',
        'description' => 'Saved description',
        'isYoutubeVideo' => 1,
        'youtubeUrl' => 'saved123',
        'videoDuration' => '1:00',
        'videoFile' => null,
        'videoCredit' => 'Saved credit',
        'viewCount' => 7,
        'metaTag' => 'saved-tag',
    ]);

    $response = submitVideoWithoutHeading("/edit-article/{$videoId}", [
        'article_id' => $videoId,
        'authorId' => $newAuthorId,
        'summary' => 'Changed summary',
        'description' => 'Changed description',
        'youtubeUrl' => 'changed123',
        'youtube_duration' => '3:15',
        'videoCredit' => 'Changed credit',
        'viewCount' => '42',
        'metaTag' => 'changed-tag',
    ]);

    $response
        ->assertSee('value="'.$newAuthorId.'" selected', false)
        ->assertDontSee('value="'.$savedAuthorId.'" selected', false)
        ->assertSee('value="Changed summary"', false)
        ->assertSee('>Changed description</textarea>', false)
        ->assertSee('value="changed123"', false)
        ->assertSee('value="3:15"', false)
        ->assertSee('value="Changed credit"', false)
        ->assertSee('value="42"', false)
        ->assertSee('value="changed-tag"', false)
        ->assertDontSee('Saved summary')
        ->assertDontSee('saved-tag');

    expect(DB::table('article_details')->where('id', $videoId)->value('summary'))->toBe('Saved summary');
});

it('shows the saved values when opening a video for edit', function () {
    $authorId = createAuthor('Alice');
    $videoId = createVideo([
        'authorId' => $authorId,
        'heading' => 'Saved heading',
        'summary' => 'Saved summary',
        'isYoutubeVideo' => 1,
        'youtubeUrl' => 'saved123',
        'videoDuration' => '1:00',
        'videoFile' => null,
        'metaTag' => 'saved-tag',
    ]);

    $this->get("/edit-article/{$videoId}")
        ->assertOk()
        ->assertSee('value="'.$authorId.'" selected', false)
        ->assertSee('value="Saved heading"', false)
        ->assertSee('value="Saved summary"', false)
        ->assertSee('value="saved123"', false)
        ->assertSee('value="1:00"', false)
        ->assertSee("importTags('saved-tag')", false);
});

it('shows the saved features, category and source when opening an article for edit', function () {
    $parentId = createCategory('Canada');
    $categoryId = createCategory('Ontario', $parentId);
    $featuredId = createFeature('Featured');
    $trendingId = createFeature('Trending');
    $articleId = createArticle([
        'categoryId' => $categoryId,
        'featureId' => (string) $featuredId,
        'source' => 'Saved source',
    ]);

    $html = $this->get("/edit-article/{$articleId}")->assertOk()->getContent();

    expect($html)
        ->toMatch('/name="featureId\[\]" value="'.$featuredId.'"\s+checked/')
        ->not->toMatch('/name="featureId\[\]" value="'.$trendingId.'"\s+checked/')
        ->toMatch('/name="categoryId\[\]" value="'.$categoryId.'"\s+checked/')
        ->toContain('value="Saved source"');
});

it('shows the submitted features, category and source over the saved ones after a failed article edit', function () {
    $parentId = createCategory('Canada');
    $savedCategoryId = createCategory('Ontario', $parentId);
    $newCategoryId = createCategory('Quebec', $parentId);
    $featuredId = createFeature('Featured');
    $trendingId = createFeature('Trending');
    $articleId = createArticle([
        'categoryId' => $savedCategoryId,
        'featureId' => (string) $featuredId,
        'source' => 'Saved source',
    ]);

    $html = $this->withSession(['_old_input' => [
        'featureId' => [(string) $trendingId],
        'categoryId' => [(string) $newCategoryId],
        'source' => 'Changed source',
    ]])->get("/edit-article/{$articleId}")->assertOk()->getContent();

    expect($html)
        ->toMatch('/name="featureId\[\]" value="'.$trendingId.'"\s+checked/')
        ->not->toMatch('/name="featureId\[\]" value="'.$featuredId.'"\s+checked/')
        ->toMatch('/name="categoryId\[\]" value="'.$newCategoryId.'"\s+checked/')
        ->not->toMatch('/name="categoryId\[\]" value="'.$savedCategoryId.'"\s+checked/')
        ->toContain('value="Changed source"');
});

it('leaves every feature unticked when the admin unticked them all before a failed save', function () {
    $featuredId = createFeature('Featured');
    $articleId = createArticle(['featureId' => (string) $featuredId]);

    $html = $this->withSession(['_old_input' => ['heading' => '']])
        ->get("/edit-article/{$articleId}")->assertOk()->getContent();

    expect($html)->not->toMatch('/name="featureId\[\]" value="'.$featuredId.'"\s+checked/');
});
