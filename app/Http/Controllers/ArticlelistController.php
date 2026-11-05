<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdvertisementDetail;
use App\Models\AdvertisementFileDetail;
use App\Models\ArticleDetail;
use App\Models\CategoryDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ArticlelistController extends Controller
{

    public function articlelist($id,Request $request)
    {
        $category_ids = array();
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $id)->where('isDisplayInFrontend', 1)->get();
        if (!$sub_category_data->isEmpty()) {
            foreach ($sub_category_data as $sc) {
                array_push($category_ids, $sc->id);
            }
        } else {
            array_push($category_ids, $id);
        }
        $top_slider =getArticleDatahome($category_ids, 8, "true", "false", "false");
        $slider_article=array();
        foreach(!$top_slider->isEmpty() ? $top_slider : array() as $s)
        {
            array_push( $slider_article,$s->id);
        }
        // dd($slider_article);
        $articles = getArticlelist($category_ids, null, $slider_article);


        if ($request->ajax()) {
            $view = View::make('frontend.articlelistajax', compact('articles'));
            $previous_url = "";
            $next_url = "";
            if($articles->hasMorePages()){
                $next_url = '<a href="'. $articles->nextPageUrl().'" id="next_link"><p>Next<img src="'.("../../images/frontend/generic/btn_arrow_r_0.png").'" class="ml-2"></p></a>';
            }else{
                $next_url = '<p>Next<img src="'.("../../images/frontend/generic/btn_arrow_r_0.png").'" class="ml-2"></p>';
            }
            if($articles->onFirstPage()){
                $previous_url = '<p><img src="'.("../../images/frontend/generic/btn_arrow_l_0.png").'" class="mr-2">Previous</p>';
            }else{
                $previous_url = '<a href="'.$articles->previousPageUrl().'" id="previous_link"><p><img src="'.("../../images/frontend/generic/btn_arrow_l_0.png").'" class="mr-2">Previous</p></a>';
            }
            return response()->json(['html' => $view->render(),'next_url' => $next_url,'previous_url' => $previous_url,'links' => $articles->links('frontend.paginationlink')->toHtml()]);
        }
        $category_name = CategoryDetail::select("id","name", "parentId")->where("id", $id)->first();
        $get_slider_id = get_feature_id("Slider");
        $get_feature_id = get_feature_id("Featured");



        $parent_category_id = ((isset($category_name) && isset($category_name->parentCategoryDetail)) ? $category_name->parentCategoryDetail->id : $id);
        $ad_sidebar = getAdvertisementData("sidebar", $parent_category_id);
        $ad_sidebar_responsive = getAdvertisementData("sidebar_responsive");
        $ad_middle = getAdvertisementData("middle", $parent_category_id);
        $ad_bottom = getAdvertisementData("bottom", $parent_category_id);

        $get_feature_id = get_feature_id("Featured");
        $trending =getArticleDatahome($category_ids, 200, "false", "false", "true");
        // dd($trending);


        $videos = ArticleDetail::select("id", "heading", "videoThumbnail", "videoFile", "videoDuration")->where('status', 1)->where('isVideo', 1)->where('isYoutubeVideo', 0)->orderBy('publishDate', 'desc')->take(10)->get();

        $category_id_canada = get_category_id("Canada");
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $category_id_canada)->where('isDisplayInFrontend', 1)->get();
        $category_ids = array();
        foreach (!$sub_category_data->isEmpty() ? $sub_category_data : array() as $sc) {
            array_push($category_ids, $sc->id);
        }
        $canada_news =getArticlelist($category_ids, 10, array());
    //    dd($canada_news);
        $category_id_international = get_category_id("International");
        $international_news =getArticlelist(array($category_id_international), 10, array());

        $category_id_community = get_category_id("Community");
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $category_id_community)->where('isDisplayInFrontend', 1)->get();
        $category_ids = array();
        foreach (!$sub_category_data->isEmpty() ? $sub_category_data : array() as $sc) {
            array_push($category_ids, $sc->id);
        }
        $community_news = getArticlelist($category_ids, 14, array());
    //    dd($community_news);
        return view('frontend.articlelist', compact('category_name', 'top_slider', 'ad_sidebar','ad_sidebar_responsive', 'ad_middle', 'ad_bottom', 'articles', 'trending', 'videos', 'category_id_canada', 'canada_news', 'category_id_international', 'international_news', 'category_id_community', 'community_news'));
    }
}
