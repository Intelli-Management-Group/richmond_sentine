<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdvertisementFileDetail;
use App\Models\AdvertisementDetail;
use App\Models\ArticleDetail;
use App\Models\CategoryDetail;
use App\Models\AuthorDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class VideolistController extends Controller
{
    public function index()
    {
        $videofirst = ArticleDetail::where('isVideo', 1)->where('status', 1)->orderBy('publishDate', 'desc')->first();
        return redirect(url("videos", ["$videofirst->id", str_slug(str_limit("$videofirst->heading", 50), "-")]));
    }
    public function getVideoclick($id, Request $request)
    {
        $videos = ArticleDetail::select("id", "heading", "authorId", "videoThumbnail", "isYoutubeVideo", "youtubeUrl", "videoFile", "videoDuration","publishDate", "created_at","updated_at")->where('isVideo', 1)->where('status', 1)->where("id","!=",$id)->orderBy('publishDate', 'desc')->paginate(15);
        if ($request->ajax()) {
            $view = View::make('frontend.videoPagination', compact('videos'));
            $previous_url = "";
            $next_url = "";
            if ($videos->hasMorePages()) {
                $next_url = '<a href="' . $videos->nextPageUrl() . '" id="next_link"><p>Next<img src="' . ("../../images/frontend/generic/btn_arrow_r_0.png") . '" class="ml-2"></p></a>';
            } else {
                $next_url = '<p>Next<img src="' . ("../../images/frontend/generic/btn_arrow_r_0.png") . '" class="ml-2"></p>';
            }
            if ($videos->onFirstPage()) {
                $previous_url = '<p><img src="' . ("../../images/frontend/generic/btn_arrow_l_0.png") . '" class="mr-2">Previous</p>';
            } else {
                $previous_url = '<a href="' . $videos->previousPageUrl() . '" id="previous_link"><p><img src="' . ("../../images/frontend/generic/btn_arrow_l_0.png") . '" class="mr-2">Previous</p></a>';
            }
            return response()->json(['html' => $view->render(), 'next_url' => $next_url, 'previous_url' => $previous_url, 'links' => $videos->links('frontend.paginationlink')->toHtml()]);
        }
        $video = ArticleDetail::find($id);

        $author = AuthorDetail::where('id', $video->authorId)->first();
        $view = view('frontend.videoajax', compact('video', 'author'))->render();
        $video_category_id = 0;
        $video_category_data = CategoryDetail::select("id")->where("name", "Videos")->first();
        if ($video_category_data) {
            $video_category_id = $video_category_data->id;
        }
        $ad_sidebar = getAdvertisementData("sidebar", $video_category_id);
        $ad_sidebar_responsive = getAdvertisementData("sidebar_responsive");
        $ad_middle = getAdvertisementData("middle", $video_category_id);
        $ad_bottom = getAdvertisementData("bottom", $video_category_id);
        $category_id_community = get_category_id("Community");
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $category_id_community)->where('isDisplayInFrontend', 1)->get();
        $category_ids = array();
        foreach (count((array)$sub_category_data) > 0 ? $sub_category_data : array() as $sc) {
            array_push($category_ids, $sc->id);
        }
        $community_news = getArticlelist($category_ids, 14, array());

        $trending = getArticleDatahome([], 200, "false", "false", "true");

        $category_id_canada = get_category_id("Canada");
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $category_id_canada)->where('isDisplayInFrontend', 1)->get();
        $category_ids = array();
        foreach (count((array)$sub_category_data) > 0 ? $sub_category_data : array() as $sc) {
            array_push($category_ids, $sc->id);
        }
        $canada_news =getArticlelist($category_ids, 10, array());

        $category_id_international = get_category_id("International");
        $international_news = getArticlelist(array($category_id_international), 10, array());


        return view('frontend.videos', compact('view', 'ad_sidebar', 'ad_sidebar_responsive', 'ad_middle', 'ad_bottom', 'videos', 'trending', 'category_id_community', 'community_news', 'category_id_canada', 'canada_news', 'category_id_international', 'international_news'));
    }
}
