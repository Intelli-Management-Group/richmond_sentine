<?php

namespace App\Http\Controllers;

use App\Models\ArticleDetail;
use App\Models\AuthorDetail;
use App\Models\BusinessCategory;
use App\Models\BusinessCategoryDetail;
use App\Models\BusinessDetail;
use App\Models\CategoryDetail;
use App\Models\ContactUsDetail;
use App\Models\CouponDetail;
use App\Models\DigitalEditionDetail;
use App\Models\EventDetail;
use App\Models\FooterContentDetail;
use App\Models\UserwiseStepsDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Redirect to related app/play store
     *
     */
    public function redirectToApp(Request $request)
    {
        $iphone = strpos($request->server('HTTP_USER_AGENT'), "iPhone");
        $android = strpos($request->server('HTTP_USER_AGENT'), "Android");
        $ipod = strpos($request->server('HTTP_USER_AGENT'), "iPod");

        if ($android == true) {
            return Redirect::to("https://play.google.com/store/apps/details?id=com.richmondsentinel.app");
        } else if ($iphone || $ipod == true) {
            return Redirect::to("https://apps.apple.com/us/app/richmond-sentinel-news/id1471479801");
        } else {
            return Redirect::to("https://richmondsentinel.ca");
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $get_slider_id = get_feature_id("Slider");
        $top_slider = getArticleDatahome([], 8, "false", "true", "false");
        // ArticleDetail::select("id", "heading", "landscapeImage","videoThumbnail", "videoFile")->where('status', 1)->where('isVideo', 0)->whereRaw('FIND_IN_SET('.$get_slider_id.',featureId) > 0')->orderBy('publishDate', 'desc')->take(8)->get();
        if (count((array)$top_slider) == 0) {
            $get_feature_id = get_feature_id("Featured");
            $top_slider = getArticleDatahome([], 8, "true", "false", "false");
            ArticleDetail::select("id", "heading", "landscapeImage", "videoThumbnail", "videoFile")->where('status', 1)->where('isVideo', 0)->whereRaw('FIND_IN_SET(' . $get_feature_id . ',featureId) > 0')->orderBy('publishDate', 'desc')->take(8)->get();
        }

        $latest_news = ArticleDetail::select("id", "heading")->orderBy('publishDate', 'desc')->where('status', 1)->where('isVideo', 0)->take(5)->get();

        $ad_sidebar = getAdvertisementData("sidebar");
        $ad_sidebar_responsive = getAdvertisementData("sidebar_responsive");
        $ad_middle = getAdvertisementData("middle");
        $ad_bottom = getAdvertisementData("bottom");

        $category_id_community = get_category_id("Community");
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $category_id_community)->where('isDisplayInFrontend', 1)->get();
        $category_ids = array();
        foreach (!$sub_category_data->isEmpty() ? $sub_category_data : array() as $sc) {
            array_push($category_ids, $sc->id);
        }
        
        $community_news = getArticlelist($category_ids, 14, array());
        $more_community_news = getArticlelist($category_ids, 10, array(),4);

        
        $category_id_canada = get_category_id("Canada");
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $category_id_canada)->where('isDisplayInFrontend', 1)->get();
        $category_ids = array();
        foreach (!$sub_category_data->isEmpty() ? $sub_category_data : array() as $sc) {
            array_push($category_ids, $sc->id);
        }
        
        $canada_news = getArticlelist($category_ids, 14, array());
        $more_canada_news = getArticlelist($category_ids, 10, array(),4);

        $category_id_international = get_category_id("International");
        
        $international_news = getArticlelist(array($category_id_international), 14, array());
        $more_international_news = getArticlelist(array($category_id_international), 10, array(),4);

        

        $trending = getArticleDatahome([], 56, "false", "false", "true");
        // dd($trending);
        $videos = ArticleDetail::select("id", "heading", "videoThumbnail", "videoFile", "videoDuration")->where('isVideo', 1)->where('status', 1)->where('isYoutubeVideo', 0)->orderBy('publishDate', 'desc')->take(10)->get();
        $coupons = CouponDetail::all(["id", "heading", "companyName", "thumbnailImage", "originalPrice", "discountPrice"])->take(10);
        $category_data_array = get_category_data();

        return view('frontend.home', compact('category_data_array', 'top_slider', 'ad_sidebar', 'ad_sidebar_responsive', 'ad_middle', 'ad_bottom', 'category_id_community', 'community_news', 'category_id_international', 'international_news', 'category_id_canada', 'canada_news', 'trending', 'videos', 'coupons', 'latest_news','more_community_news' , 'more_canada_news','more_international_news'));
    }

    public function articledetail($id)
    {
        $article = ArticleDetail::find($id);
        if ($article == null) {
            return redirect('/');
        }
        if ($article->status == 0) {
            return view('errors.404');
        }
        $top_slider = $article->articleFileDetail;

        $article->timestamps = false;
        $article->increment('viewCount');
        $cat = CategoryDetail::where('id', $article->categoryId)->first();
        $category_id = (isset($cat->parentCategoryDetail) ? $cat->parentCategoryDetail->id : $article->categoryId);
        $author = AuthorDetail::where('id', $article->authorId)->first();
        $category_name = CategoryDetail::select("name")->where("id", $id)->first();

        $ad_sidebar = getAdvertisementData("sidebar", $category_id);
        $ad_sidebar_responsive = getAdvertisementData("sidebar_responsive", $category_id);
        $ad_middle = getAdvertisementData("middle", $category_id);
        $ad_bottom = getAdvertisementData("bottom", $category_id);

        $trending = getArticleDatahome(array($article->categoryId), 200, "false", "false", "true");
        $coupons = CouponDetail::all(["id", "heading", "companyName", "thumbnailImage", "originalPrice", "discountPrice"])->take(10);
        $category_id_canada = get_category_id("Canada");
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $category_id_canada)->where('isDisplayInFrontend', 1)->get();
        $category_ids = array();
        foreach (!$sub_category_data->isEmpty() ? $sub_category_data : array() as $sc) {
            array_push($category_ids, $sc->id);
        }
        $canada_news = getArticlelist($category_ids, 10, array());
        //  dd($canada_news);
        //  ArticleDetail::select("id", "heading", "landscapeImage", "categoryId")->whereIn('categoryId', $category_ids)->where('status', 1)->orderBy('publishDate', 'desc')->take(10)->get();

        $category_id_international = get_category_id("International");

        $international_news = getArticlelist(array($category_id_international), 10, array());

        $category_id_community = get_category_id("Community");
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $category_id_community)->where('isDisplayInFrontend', 1)->get();
        $category_ids = array();
        foreach (!$sub_category_data->isEmpty() ? $sub_category_data : array() as $sc) {
            array_push($category_ids, $sc->id);
        }
        $community_news = getArticlelist($category_ids, 10, array());

        return view('frontend.articledetail', compact('article', 'top_slider', 'cat', 'author', 'ad_sidebar', 'ad_sidebar_responsive', 'ad_middle', 'ad_bottom', 'trending', 'coupons', 'category_id_canada', 'canada_news', 'category_id_international', 'international_news', 'category_id_community', 'community_news', 'category_name'));
    }

    public function aboutus()
    {
        $aboutus = FooterContentDetail::where('id', '1')->first();
        $contact = ContactUsDetail::where('id', '1')->first();
        return view('frontend.aboutus', compact('aboutus', 'contact'));
    }

    public function termsofuse()
    {
        $tou = FooterContentDetail::where('id', '2')->first();
        return view('frontend.termsofuse', compact('tou'));
    }

    public function privacypolicy()
    {
        $policy = FooterContentDetail::where('id', '3')->first();
        return view('frontend.privacypolicy', compact('policy'));
    }

    public function contactus()
    {
        $contact = ContactUsDetail::where('id', '1')->first();
        return view('frontend.contactus', compact('contact'));
    }

    public function placeanad()
    {
        $contact = ContactUsDetail::where('id', '1')->first();
        return view('frontend.placeanad', compact('contact'));
    }

    public function editions()
    {
        $category_id = "";
        $category_data = CategoryDetail::select("id")->where("name", "Editions")->first();
        if ($category_data) {
            $category_id = $category_data->id;
        }

        $ad_sidebar = getAdvertisementData("sidebar", $category_id);
        $ad_sidebar_responsive = getAdvertisementData("sidebar_responsive", $category_id);

        $edition_data = DigitalEditionDetail::orderBy("editionDate", "desc")->get();
        $editions = array();
        foreach (count((array)$edition_data) > 0 ? $edition_data : array() as $e) {
            $edition_date = Carbon::parse($e->editionDate);
            $year = $edition_date->format("Y");
            if (!isset($editions[$year])) {
                $editions[$year] = array();
            }
            array_push($editions[$year], array(
                'name' => $e->name,
                'thumbnailImage' => ("../uploads/editions/" . $e->thumbnailImage),
                'volumeEdition' => $e->volumeEdition,
                'editionNumber' => $e->editionNumber,
                'editionDate' => $e->editionDate,
                'pdfFile' => ("../uploads/editions/" . $e->pdfFile),
            ));
        }
        return view('frontend.editions', compact("editions", "ad_sidebar", "ad_sidebar_responsive"));
    }

    public function searchList()
    {
        $searchvalue = trim((string) request("searchvalue"));
        $searchvalue = strtolower($searchvalue);


        $article_data = ArticleDetail::selectRaw("article_details.id,article_details.heading,article_details.summary,article_details.categoryId,article_details.authorId,article_details.publishDate,article_details.updated_at,article_details.status,article_details.videoFile,article_details.videoThumbnail,article_details.isVideo")->leftJoin("category_details", function ($join) {
            $join->on('article_details.categoryId', '=', 'category_details.id');
        })
            ->leftJoin("author_details", function ($join) {
                $join->on('article_details.authorId', '=', 'author_details.id');
            })
            ->where("article_details.status", 1)->orderBy('publishDate', 'desc');

        $ad_sidebar = getAdvertisementData("sidebar");
        $ad_sidebar_responsive = getAdvertisementData("sidebar_responsive");
        if ($searchvalue != "") {
            $article_list = $article_data->whereAny(["article_details.heading", "article_details.description", "article_details.summary", "category_details.name", "author_details.name"], "like", "%" . $searchvalue . "%")->paginate(10);
            if (count((array)$article_list) == 0) {
                $msg = "Search Data Not Found";
                return view('frontend.searchlist', compact('msg', 'ad_sidebar', 'ad_sidebar_responsive'));
            }

            $pages = ($article_list->appends(['searchvalue' => $searchvalue])->getUrlRange(1, $article_list->lastPage()));
            return view('frontend.searchlist', compact('searchvalue', 'article_list', 'pages', 'ad_sidebar', 'ad_sidebar_responsive'));
        } else {
            $msg = "Search Data Not Found";
            return view('frontend.searchlist', compact('msg', 'ad_sidebar', 'ad_sidebar_responsive'));
        }

    }

    public function getStores()
    {
        $all_listing = $this->getBusinessData();
        return view('frontend.storeList', compact("all_listing"));
    }

    public function stepUpLeaderBoard()
    {
        $all_users_data = UserwiseStepsDetail::orderBy("totalSteps", "desc")->take(10)->get();
        $data = array();
        $rank = 1;
        foreach (!$all_users_data->isEmpty() ? $all_users_data : array() as $u) {
            if ($u->userDetail) {
                $user_data = $u->userDetail;
                array_push($data, array("rank" => $rank, "avatar" => getUserAvatar($user_data->avatar), "name" => $user_data->name, "totalSteps" => $u->totalSteps));
                $rank++;
            }
        }
        $coupons = CouponDetail::all(["id", "heading", "companyName", "thumbnailImage", "originalPrice", "discountPrice"])->take(10);
        $ad_sidebar = getAdvertisementData("sidebar");
        $ad_sidebar_responsive = getAdvertisementData("sidebar_responsive");
        $trending = getArticleDatahome(array(), 15, "false", "false", "true");
        $ad_bottom = getAdvertisementData("bottom");
        return view('frontend.leaderboard', compact("data", "coupons", "ad_sidebar", "ad_sidebar_responsive", "trending", "ad_bottom"));
    }

    public function getBusinessData()
    {
        $search_str = request("search_string");
        $all_categories = BusinessCategory::where("isActive", 1)->orderBy("name", "asc")->get();
        $categories = $alphabetical_categories = $featured = array();
        foreach (count((array)$all_categories) > 0 ? $all_categories : array() as $c) {
            $business_details = BusinessCategoryDetail::join("business_details", "business_details.id", "=", "business_category_details.businessId")->selectRaw("business_details.*")->where("business_category_details.categoryId", $c->id)->whereRaw("business_details.deleted_at is null");
            if ($search_str != "") {
                $business_details = $business_details->where("business_details.name", "like", "%" . $search_str . "%");
            }
            $business_details = $business_details->get();
            $businesses = array();
            foreach (count((array)$business_details) > 0 ? $business_details : array() as $b) {
                $data = array("id" => $b->id, "name" => $b->name, "shortCode" => $b->shortCode, "phone" => $b->phone, "address" => getBusinessAddress($b), "logo" => getBusinessImage($b->logo), "email" => $b->email, "website" => $b->website);
                $first_letter = strtoupper(substr($b->name, 0, 1));
                if (!isset($alphabetical_categories[$first_letter]))
                    $alphabetical_categories[$first_letter] = array("name" => $first_letter, "childs" => array());

                if (!in_array($b->id, array_keys($alphabetical_categories[$first_letter]["childs"]))) {
                    $alphabetical_categories[$first_letter]["childs"][$b->id] = $data;
                }
                array_push($businesses, $data);

            }
            if ($search_str != "") {
                if (count($businesses) > 0) {
                    array_push($categories, array("id" => $c->id, "name" => $c->name, "childs" => $businesses));
                }
            } else {
                array_push($categories, array("id" => $c->id, "name" => $c->name, "childs" => $businesses));
            }

        }
        $featured_business = BusinessDetail::where("isFeatured", 1);
        if ($search_str != "") {
            $featured_business = $featured_business->where("name", "like", "%" . $search_str . "%");
        }
        $featured_business = $featured_business->get();
        foreach (count((array)$featured_business) > 0 ? $featured_business : array() as $f) {
            array_push($featured, array("id" => $f->id, "name" => $f->name, "shortCode" => $f->shortCode, "phone" => $f->phone, "address" => getBusinessAddress($f), "logo" => getBusinessImage($f->logo), "email" => $f->email, "website" => $f->website));
        }
        ksort($alphabetical_categories);
        $all_listing = view("frontend.searchBusinessList", compact("featured", "categories", "alphabetical_categories"));
        return $all_listing;
    }

    public function getBusinessDetail($short_code)
    {
        if ($short_code == null) {
            return view('errors.404');
        }
        $business_data = BusinessDetail::where("shortCode", $short_code)->first();
        if (!$business_data) {
            return view('errors.404');
        }
        $categories = BusinessCategoryDetail::where("businessId", $business_data->id)->get();
        $business_categories = array();
        foreach (count((array)$categories) > 0 ? $categories : array() as $c) {
            if ($c->categoryDetail) {
                array_push($business_categories, $c->categoryDetail->name);
            }
        }
        $business_hours = json_decode($business_data->businessHours ?? "");
        $related_articles = getRelatedArticles($business_data->id, $business_data->name)["article"];
        $events = EventDetail::where("businessId", $business_data->id)->get();
        $event_array = array();
        $business_data->logo = getBusinessImage($business_data->logo);
        $business_data->address = getBusinessAddress($business_data);
        foreach (count((array)$events) > 0 ? $events : array() as $e) {
            $price = json_decode($e->price);
            array_push($event_array, array("id" => $e->id, "banner_image" => getEventBanner($e->bannerImage), "name" => $e->name, "event_date" => Carbon::parse($business_data->eventDate)->format("M d, Y") . " " . Carbon::parse($business_data->eventTime)->format("h:i a"), "price" => config("constants.event_price_option")[$price->option], "cost" => $price->value, "link_text" => $e->linkText, "booking_link" => $e->bookingLink));
        }
        $business_photos = isset($business_data->imageDetail) ? $business_data->imageDetail : array();
        $coupons = CouponDetail::all(["id", "heading", "companyName", "thumbnailImage", "originalPrice", "discountPrice"])->take(10);
        $ad_sidebar = getAdvertisementData("sidebar");
        $ad_sidebar_responsive = getAdvertisementData("sidebar_responsive");
        $trending = getArticleDatahome(array(), 30, "false", "false", "true");
        $ad_bottom = getAdvertisementData("bottom");
        return view('frontend.businessDetail', compact('business_data', 'business_categories', 'business_hours', 'related_articles', 'event_array', 'business_photos', 'coupons', 'ad_sidebar', 'ad_sidebar_responsive', 'trending', 'ad_bottom'));
    }

    public function getAllBusiness(Request $request)
    {
        $data = array();
        $all_business = BusinessDetail::where("name", "like", "%" . $request->input("term") . "%")->get();
        foreach (count((array)$all_business) > 0 ? $all_business : array() as $b) {
            $single_data = "<div class='d-flex'><div class='w-5 text-center'><img src='" . getBusinessImage($b->logo) . "' alt='$b->name' class='business-logo-small'/></div><div class='ml-3'><strong>$b->name</strong><div>" . getBusinessAddress($b) . "</div></div></div>";
            array_push($data, array("shortCode" => $b->shortCode, "value" => $b->name, "label" => $single_data));
        }
        return json_encode($data);
    }

    public function getEvents()
    {
        return view('frontend.eventList');
    }

    public function getEventList()
    {
        $data = array();
        $search_date = \request("event_date") ?? Carbon::now()->format("Y-m-d");
        $events = EventDetail::whereRaw('DATE_FORMAT(`eventDate`, "%Y-%m") = ? AND eventDate >= ?', [Carbon::parse($search_date)->format("Y-m"), $search_date])->orderBy("eventDate", "asc")->orderBy("eventTime", "asc")->get();

        if (Carbon::parse($search_date)->isCurrentYear() && Carbon::parse($search_date)->isCurrentMonth()) {
            $today_date = Carbon::now()->format("Y-m-d");
            if (!isset($data[$today_date])) {
                $data[$today_date] = array();
            }
        }
        foreach (count((array)$events) > 0 ? $events : array() as $e) {
            if (!isset($data[$e->eventDate])) {
                $data[$e->eventDate] = array();
            }
            $price = json_decode($e->price);
            array_push($data[$e->eventDate], array("id" => $e->id, "name" => $e->name, "date" => $e->eventDate, "time" => ($e->eventTime != null ? Carbon::parse($e->eventTime)->format("h:i A") : ""), "linkText" => $e->linkText, "bookingLink" => $e->bookingLink, "price" => config("constants.event_price_option")[$price->option], "cost" => $price->value));
        }
        ksort($data);
        $view = view("frontend.upcomingEvent", compact("data"))->render();
        return json_encode(array("data" => $data, "view" => $view));
    }
    
     public function loadElectionPage(){
        $mayor_tab = array('2022 City of Richmond Election for Mayor – Malcolm Brodie' => 'Gg1MOna_PjU', '2022 City of Richmond Election for Mayor - John Roston' => "Nqd-bIgjYxY");
        $councillor_tab = array(
            '2022 City of Richmond Election for Councillor – Alexa Loo' => 'GVtKOBBwQ0I',
            '2022 City of Richmond Election for Councillor – Andy Hobbs' => 'UBO5OI1oSCo',
            '2022 City of Richmond Election for Councillor – Bill Han' => 'ATYKMTb4Bm0',
            '2022 City of Richmond Election for Councillor – Bill McNulty' => 'd4C4-wLqfY0',
            '2022 City of Richmond Election for Councillor – Carol Day' => 'F3Px0GmdSzw',
            '2022 City of Richmond Election for Councillor – Chai Chung' => 'WjmleiA34r0',
            '2022 City of Richmond Election for Councillor – Chak Au' => 'vxxdiej4CFU',
            '2022 City of Richmond Election for Councillor – Dennis Page' => 'TNAL_p48XMA',
            '2022 City of Richmond Election for Councillor – Derek Dang' => 'LdVCa5MPiqo',
            '2022 City of Richmond Election for Councillor – Elsa Wong' => 'YCL9mkb1D-s',
            '2022 City of Richmond Election for Councillor – Eric Yung' => 'PJOM4CVcDuI',
            '2022 City of Richmond Election for Councillor – Evan Dunfee' => 'r_us1STpIh8',
            '2022 City of Richmond Election for Councillor – Fipe Wong' => 'vkMzKCDwiGk',
            '2022 City of Richmond Election for Councillor – Jack Trovato' => 'GJAjFPdvwqU',
            '2022 City of Richmond Election for Councillor – Jasmine Piao' => '61NqI1fkEns',
            '2022 City of Richmond Election for Councillor – Jerome Dickey' => 'oQGb9roMiAE',
            '2022 City of Richmond Election for Councillor – Kash Heed' => 'cYshnDQkmOY',
            '2022 City of Richmond Election for Councillor – Keefer Pelech' => 'C2B94WYiNWo',
            '2022 City of Richmond Election for Councillor – Laura Gillanders' => 'q1q9LIC0aL0',
            '2022 City of Richmond Election for Councillor – Mark Lee' => '-MfmCGRRcX8',
            '2022 City of Richmond Election for Councillor – Melissa Zhang' => 'yE0PVP77C5g',
            '2022 City of Richmond Election for Councillor – Michael Wolfe' => 'YQ3GSd616A4',
            '2022 City of Richmond Election for Councillor – Mohamud Ali Farah' => '6VC4Mae8Hm8',
            '2022 City of Richmond Election for Councillor – Rahim Othman' => 'IatQBrVwysI',
            '2022 City of Richmond Election for Councillor – Sheldon Starrett' => 'zRYFZ172Sh8',
            '2022 City of Richmond Election for Councillor – Adil Awan' => 'IUnTUtG0r7A',
        );
        $trustee_tab = array(
            'Councillor Linda McPhail in an Interview re 2022 Election' => 'QKr6jA8fKhg',
            '2022 City of Richmond Election for School Trustee – Alice Wong' => '-uhKJBI2FV8',
            '2022 City of Richmond Election for School Trustee – David Yang' => 'FyImFkUdrj8',
            '2022 City of Richmond Election for School Trustee – Dean Billings' => 'Tm6_6rtF_ao',
            '2022 City of Richmond Election for School Trustee – Debbie Tablotney' => 'WQNOKAZGgMQ',
            '2022 City of Richmond Election for School Trustee –Heather Larson' => 'Rk43Ym2OeSo',
            '2022 City of Richmond Election for School Trustee – Kay Hale' => 'vdNIegtBue0',
            '2022 City of Richmond Election for School Trustee – Ken Hamaguchi' => 'dROe9WP2nFQ',
            '2022 City of Richmond Election for School Trustee – Linda Li' => 'k_UwFZzQD4I',
            '2022 City of Richmond Election for School Trustee – Rachel Ling' => 'b-Scx7Lhvao',
            '2022 City of Richmond Election for School Trustee – Rajan Paul' => 'iqbDWN3JA_g',
            '2022 City of Richmond Election for School Trustee – Richard Lee' => '2FF-VV-ze4M',
            '2022 City of Richmond Election for School Trustee – Rod Belleza' => 'rvTv0KuJ8oc',
            '2022 City of Richmond Election for School Trustee – Donna Sargent' => 'MPWJfxbXLAc',
        );
        return view('frontend.election2022', compact('mayor_tab','councillor_tab','trustee_tab'));
    }
    
    public function loadNewElectionPage($type = 'city')
    {
       $city_2022_mayor_tab = array('2022 City of Richmond Election for Mayor – Malcolm Brodie' => 'Gg1MOna_PjU', '2022 City of Richmond Election for Mayor' => "Nqd-bIgjYxY");
        $city_2022_councillor_tab = array(
            '2022 City of Richmond Election for Councillor – Alexa Loo' => 'GVtKOBBwQ0I',
            '2022 City of Richmond Election for Councillor – Andy Hobbs' => 'UBO5OI1oSCo',
            '2022 City of Richmond Election for Councillor – Bill Han' => 'ATYKMTb4Bm0',
            '2022 City of Richmond Election for Councillor – Bill McNulty' => 'd4C4-wLqfY0',
            '2022 City of Richmond Election for Councillor – Carol Day' => 'F3Px0GmdSzw',
            '2022 City of Richmond Election for Councillor – Chai Chung' => 'WjmleiA34r0',
            '2022 City of Richmond Election for Councillor – Chak Au' => 'vxxdiej4CFU',
            '2022 City of Richmond Election for Councillor – Dennis Page' => 'TNAL_p48XMA',
            '2022 City of Richmond Election for Councillor – Derek Dang' => 'LdVCa5MPiqo',
            '2022 City of Richmond Election for Councillor – Elsa Wong' => 'YCL9mkb1D-s',
            '2022 City of Richmond Election for Councillor – Eric Yung' => 'PJOM4CVcDuI',
            '2022 City of Richmond Election for Councillor – Evan Dunfee' => 'r_us1STpIh8',
            '2022 City of Richmond Election for Councillor – Fipe Wong' => 'vkMzKCDwiGk',
            '2022 City of Richmond Election for Councillor – Jack Trovato' => 'GJAjFPdvwqU',
            '2022 City of Richmond Election for Councillor – Jasmine Piao' => '61NqI1fkEns',
            '2022 City of Richmond Election for Councillor – Jerome Dickey' => 'oQGb9roMiAE',
            '2022 City of Richmond Election for Councillor – Kash Heed' => 'cYshnDQkmOY',
            '2022 City of Richmond Election for Councillor – Keefer Pelech' => 'C2B94WYiNWo',
            '2022 City of Richmond Election for Councillor – Laura Gillanders' => 'q1q9LIC0aL0',
            '2022 City of Richmond Election for Councillor – Mark Lee' => '-MfmCGRRcX8',
            '2022 City of Richmond Election for Councillor – Melissa Zhang' => 'yE0PVP77C5g',
            '2022 City of Richmond Election for Councillor – Michael Wolfe' => 'YQ3GSd616A4',
            '2022 City of Richmond Election for Councillor – Mohamud Farah' => '6VC4Mae8Hm8',
            '2022 City of Richmond Election for Councillor – Rahim Othman' => 'IatQBrVwysI',
            '2022 City of Richmond Election for Councillor – Sheldon Starrett' => 'zRYFZ172Sh8',
            '2022 City of Richmond Election for Councillor – Adil Awan' => 'IUnTUtG0r7A',
        );
        $city_2022_trustee_tab = array(
            'Councillor Linda McPhail in an Interview re 2022 Election' => 'QKr6jA8fKhg',
            '2022 City of Richmond Election for School Trustee – Alice Wong' => '-uhKJBI2FV8',
            '2022 City of Richmond Election for School Trustee – David Yang' => 'FyImFkUdrj8',
            '2022 City of Richmond Election for School Trustee – Dean Billings' => 'Tm6_6rtF_ao',
            '2022 City of Richmond Election for School Trustee – Debbie Tablotney' => 'WQNOKAZGgMQ',
            '2022 City of Richmond Election for School Trustee –Heather Larson' => 'Rk43Ym2OeSo',
            '2022 City of Richmond Election for School Trustee – Kay Hale' => 'vdNIegtBue0',
            '2022 City of Richmond Election for School Trustee – Ken Hamaguchi' => 'dROe9WP2nFQ',
            '2022 City of Richmond Election for School Trustee – Linda Li' => 'k_UwFZzQD4I',
            '2022 City of Richmond Election for School Trustee – Rachel Ling' => 'b-Scx7Lhvao',
            '2022 City of Richmond Election for School Trustee – Rajan Paul' => 'iqbDWN3JA_g',
            '2022 City of Richmond Election for School Trustee – Richard Lee' => '2FF-VV-ze4M',
            '2022 City of Richmond Election for School Trustee – Rod Belleza' => 'rvTv0KuJ8oc',
            '2022 City of Richmond Election for School Trustee – Donna Sargent' => 'MPWJfxbXLAc',
        );
        $city_2021_tab = array("Richmond BC By-Election" => 'f-iUwfHY0K0');
        $city_2018_trustee_tab = array('Rod Belleza | Richmond Community Coalition' => 'g1RdlUVngPQ', 'Keith Liedtke | Richmond Community Coalition' => 'V0VZbztyDJ8', 'Harv Puni | Richmond Community Coalition' => '89AOTrSojxU', 'Ken Hamaguchi | Richmond Education Party' => 'XEifF7uGo5c', 'Jeffrey Smith | Independent' => 'S9LFICZNswc', 'Grace Tsang | Richmond Community Coalition' => 'b8vSTWu6xk4', 'Sandra Nixon | Richmond Education Party' => 'wKcLMpkeYKM', 'Karina Reid | Richmond Education Party' => 'WU-XIZQu8kY', 'Andrew Scallion | Richmond Education Party' => 'KkVLB9SGFVg', 'Norm Goldstein | Richmond First' => 's0VoebJoZSY', 'Donna Sargent | Richmond First' => 'XYaLLdO4sIE', 'Heather Larson | Richmond Education Party' => 'h5iUATjRILM', 'Elsa Wong | Richmond First' => '-uAn477_BXg', 'Eric Yung | Richmond First' => 'uwSG564itDg', 'Rahim Othman | Richmond Community Coalition' => 'xXx5xzZxEIg', 'Andrea Gong-Quinn | Independent' => '6UOqSf_BETg', 'Alice S. Wong | Independent' => 'zp1XcTjTy4w', 'Sharon Wang | Independent' => 'JH7LSEJb3OA', 'Debbie Tablotney | Independent' => 'U4exBZHpaMY', 'Charvine Adl | Independent' => 'db3LSocXQ9Q', 'Ivan Pak | Independent' => 'Bms61jFj7vU', 'James Li' => 'EI2rR0K_bdk', 'Richard Lee' => '967WjRCVTpw', 'Sergio Arrambide' => 'sGIROS1lR_g');
        $city_2018_councillor_tab = array('Kelly Greene | Richmond Citizens Association (RCA)' => 'M9vopwTC7sQ', 'Judie Schneider | Richmond Citizens Association (RCA)' => 'yp13pj4TLM0', 'Chak Au | Richmond Community Coalition' => 'K5rM0HdfVV4', 'Parm Bains | Richmond Community Coalition' => 'nbcHw-mvBks', 'Jonathan Ho | Richmond Community Coalition
' => 'JfqU_HQdWxU', 'Ken Johnston | Richmond Community Coalition' => 'ITahdCofa74', 'Melissa Zhang | Richmond Community Coalition' => 'nrp-kcj527I', 'Derek Dang | Richmond First' => 'M4K8I37PzHQ', 'Sunny Ho | Richmond First' => '2pz2m2igUtw', 'Peter Liu | Richmond First' => 'ly_m3tkTTc0', 'Bill McNulty | Richmond First' => 'O39Le0dV0OQ', 'Carol Day | RITE' => 'zyt_ySGZoMI', 'Niti Sharma | RITE' => 'xL4WALmc1t0', 'Michael Wolfe | RITE' => 'd1pqjJV8Qa4', 'Henry Yao | RITE' => 'Br1cZvr5Kz8', 'Adil Awan | Independent' => 'oXFONiW6OlY', 'Theresa Head | Independent' => 'lBb59NzUItw', 'Alexa Loo | Independent' => 'EfMIhh-Kipw', 'Dennis Page | Independent' => 'AMzZoEi889M', 'John Roston | Independent' => '65VXOzHy3mA', 'Patrick S. Saunders | | Independent' => 'bJ6KXdCKbH0', 'Manjit Singh | | Independent' => 'g7Tsw6I4aas', 'Kerry Starchuk | Independent' => 'XGKEDbzj9SE', 'Jason Tarnow | Independent' => 'rrGsGFnNAOk', 'Andy Hobbs' => 'taidryOM9EU');
        $city_2018_mayor_tab = array('Hong Guo | Independent' => 'dxMXosRBxOM', 'Roy Sakata | Independent' => 'qrQmEbhvNmQ', 'Cliff Wei | Independent' => 'QL3OhgHTcmo', 'Donald Flintoff | Independent' => 'AEWyEK8_5tI', 'Malcom Brodie | Independent' => 'ZWG9OHsZiEs');
        $provincial_tab = array("Richmond Candidates' Interviews for 2020 BC Election" => '693ne2kQ2zg');
        return view('frontend.election', compact('city_2022_mayor_tab', 'city_2022_councillor_tab', 'city_2022_trustee_tab', 'type','city_2021_tab', 'city_2018_trustee_tab', 'city_2018_councillor_tab', 'city_2018_mayor_tab', 'provincial_tab'));
    }

}
