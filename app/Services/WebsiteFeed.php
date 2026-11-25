<?php

namespace App\Services;

use App\Models\ArticleDetail;
use App\Models\CategoryDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Picks articles with the same rules as the website pages, so the app API can
 * show what the website shows. It reuses the website's helpers in app/Helpers.php
 * where it can; where the website builds a query inline, the source is named.
 *
 * Every *Ids method returns unique article ids in the order the website shows
 * them. The website's queries join article_file_details, so an article with two
 * main media rows comes back twice; it is listed once here.
 */
final class WebsiteFeed
{
    /** HomeController::index top slider. */
    public const HOME_SLIDER_LIMIT = 8;

    /** ArticlelistController::articlelist top slider. */
    public const CATEGORY_SLIDER_LIMIT = 8;

    /** HomeController::index "Latest News" ticker. */
    public const LATEST_NEWS_LIMIT = 5;

    /** HomeController::index Community, Canada and International blocks. */
    public const HOME_SECTION_LIMIT = 14;

    /**
     * The home page slider: newest articles tagged Slider.
     */
    public function homeSliderIds(int $limit = self::HOME_SLIDER_LIMIT): array
    {
        return $this->ids(getArticleDatahome([], $limit, "false", "true", "false"));
    }

    /**
     * The home page "Latest News" ticker (HomeController::index).
     */
    public function latestNewsIds(int $limit = self::LATEST_NEWS_LIMIT): array
    {
        return $this->ids(
            ArticleDetail::select("id")->where('status', 1)->where('isVideo', 0)->orderBy('publishDate', 'desc')->take($limit)->get()
        );
    }

    /**
     * The home page Community, Canada and International blocks, in that order.
     * Community and Canada use their subcategories shown on the website;
     * International uses only its own category, as HomeController::index does.
     *
     * @return list<array{category_id: int, name: string, article_ids: list<int>}>
     */
    public function homeSections(): array
    {
        $sections = array();
        foreach (array("Community", "Canada") as $name) {
            $categoryId = (int) get_category_id($name);
            $sections[] = $this->section($categoryId, $name, $this->frontendChildIds($categoryId));
        }
        $internationalId = (int) get_category_id("International");
        $sections[] = $this->section($internationalId, "International", array($internationalId));

        return $sections;
    }

    /**
     * The categories a website category page lists (ArticlelistController::articlelist):
     * its subcategories shown on the website, or the category itself when it has none.
     */
    public function categoryIdsFor(int $categoryId): array
    {
        $childIds = $this->frontendChildIds($categoryId);

        return count($childIds) > 0 ? $childIds : array($categoryId);
    }

    /**
     * A website category page's slider: newest articles tagged Featured in those categories.
     */
    public function categorySliderIds(array $categoryIds, int $limit = self::CATEGORY_SLIDER_LIMIT): array
    {
        return $this->ids(getArticleDatahome($categoryIds, $limit, "true", "false", "false"));
    }

    /**
     * A website category page's article list (getArticlelist), newest first,
     * as a query the caller can count and page.
     */
    public function listQuery(array $categoryIds, array $excludeIds): Builder
    {
        $query = ArticleDetail::query()
            ->where('status', 1)
            ->where('isVideo', 0)
            ->whereIn('categoryId', $categoryIds)
            ->whereHas('articleFileDetail', function ($q) {
                $q->where('isMain', 1);
            });
        if (count($excludeIds) > 0) {
            $query->whereNotIn('id', $excludeIds);
        }

        return $query->orderBy('publishDate', 'desc');
    }

    /**
     * Full article models for the ids, in the order given, with the relations
     * the API's itereateArticleData reads, so serializing them costs no query per article.
     *
     * @return Collection<int, ArticleDetail>
     */
    public function load(array $ids): Collection
    {
        if (count($ids) == 0) {
            return collect();
        }
        $position = array_flip(array_values($ids));

        return ArticleDetail::with(['authorDetail', 'articleFileDetail', 'categoryDetail.parentCategoryDetail'])
            ->whereIn('id', $ids)->get()
            ->sortBy(fn ($article) => $position[$article->id])
            ->values();
    }

    private function section(int $categoryId, string $name, array $categoryIds): array
    {
        return array(
            "category_id" => $categoryId,
            "name" => $name,
            "article_ids" => $this->ids(getArticlelist($categoryIds, self::HOME_SECTION_LIMIT, array())),
        );
    }

    private function frontendChildIds(int $categoryId): array
    {
        return CategoryDetail::where("parentId", $categoryId)->where('isDisplayInFrontend', 1)->orderBy('id')
            ->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    private function ids($rows): array
    {
        return $rows->pluck('id')->map(fn ($id) => (int) $id)->unique()->values()->all();
    }
}
