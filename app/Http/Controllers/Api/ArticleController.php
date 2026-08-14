<?php

namespace App\Http\Controllers\Api;

use App\Models\AdvertisementDetail;
use App\Models\AppUserDetail;
use App\Models\ArticleDetail;
use App\Models\AuthorDetail;
use App\Models\BusinessCategoryDetail;
use App\Models\BusinessDetail;
use App\Models\BusinessPhotosDetail;
use App\Models\CategoryDetail;
use App\Models\ContactUsDetail;
use App\Models\CouponDetail;
use App\Models\EventDetail;
use App\Models\StepsDetail;
use App\Models\UserCategoryNotificationDetail;
use App\Models\UserwiseArticleBookmarkDetail;
use App\Models\UserwiseArticleReadDetail;
use App\Models\UserwiseStepsDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Pushok\AuthProvider;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\AdvertisementFileDetail;
use Notification as mailnotification;
use App\Notifications\VerificationCode;
use Illuminate\Support\Facades\File;
use Twilio\Rest\Client as Tclient;

class ArticleController extends Controller
{
    /**
     *  Method to get category wise article data
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getCategoryData(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $is_app = session("user_details")->is_app;

            $category_data = CategoryDetail::selectRaw("id,name,iconImage,isActualCategory,parentId,level,isDisplayInMore,isDisplayInMenu,nav_menu_index")->where("isDisplayInApp", $is_app)->where("isDisplayInMenu", 1)->where("level", 0)->orderBy("nav_menu_index")->get()->toArray();
            $menu_category_data = $this->iterateCategory($category_data, $user_id, $is_app, 1);

            $category_data = CategoryDetail::selectRaw("id,name,iconImage,isActualCategory,parentId,level,isDisplayInMore,isDisplayInMenu,more_section_index")->where("isDisplayInApp", $is_app)->where("isDisplayInMore", 1)->where("level", 0)->orderBy("more_section_index")->get()->toArray();
            $more_category_data = $this->iterateCategory($category_data, $user_id, $is_app, 0);

            $category_data = CategoryDetail::selectRaw("id,name,iconImage,isActualCategory,parentId,level,isCouponCategory,isDisplayInMore,isDisplayInMenu")->where("isDisplayInApp", $is_app)->where("isCouponCategory", 1)->where("level", 0)->orderBy("id")->get()->toArray();
            $coupon_category_data = array();
            foreach ((count($category_data) > 0 ? $category_data : array()) as $level0) {
                $level0["id"] = intval($level0["id"]);
                $level0["parentId"] = intval($level0["parentId"]);
                $level0["iconImage"] = ($level0["iconImage"] != "" ? (url('images/' . $level0["iconImage"])) : "");

                $userwise_category_notification = UserCategoryNotificationDetail::select("isNotificationOn")->where("userId", $user_id)->where("categoryId", $level0["id"])->first();
                if ($userwise_category_notification) {
                    $level0["isNotificationOn"] = intval($userwise_category_notification->isNotificationOn);
                } else {
                    $level0["isNotificationOn"] = 1;
                }
                $single_category = $level0;
                unset($single_category["isCouponCategory"]);

                $single_category["childList"] = array();
                array_push($coupon_category_data, $single_category);
            }

            $content_data = array();
            $contact_us_data = ContactUsDetail::selectRaw("email_id,about_us_email,phone,place_an_ad_phone,office_days,office_hours,mailing_address")->first();
            if ($contact_us_data) {
                $content_data = $contact_us_data;
                $content_data["place_an_ad_phone"] = explode(",", $content_data->place_an_ad_phone);
                $content_data["place_an_ad_image"] = asset("images/frontend/generic/place_ad_ad_mobile.jpg");
                $content_data["place_an_ad_image_tablet"] = asset("images/frontend/generic/place_an_ad_tablet.jpg");
                $content_data["google_map_url"] = "https://www.google.com/maps/d/u/0/embed?mid=1D2Xlmy-e_rmvtnVQEgjdwaceA_-tUGP0";
            }
            $total_steps = 0;
            if ($request->input("stepDate") && session("user_details")->isVerified) {
                $datewise_data = StepsDetail::where("userId", $user_id)->where("stepDate", $request->input("stepDate"))->first();
                if ($datewise_data) {
                    $total_steps = intval($datewise_data->totalSteps);
                }
            }
            return $this->sendResultJSON("1", "", array("coupon_category_list" => $coupon_category_data, 'author_data' => $this->getAllAuthorName(), "contact_us_data" => $content_data, "menu_category" => $menu_category_data, "more_category" => $more_category_data, "name" => session("user_details")->name, "email" => session("user_details")->email, "phoneNo" => session("user_details")->phoneNo, "avatar" => getUserAvatar(session("user_details")->avatar), "isVerified" => session("user_details")->isVerified, "updatedAt" => (isset(session("user_details")->stepDetail) ? Carbon::parse(session("user_details")->stepDetail->created_at)->format("Y-m-d H:i:s") : ""), "totalSteps" => $total_steps));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     * Method to iterate category data
     */
    private function iterateCategory($category_data, $user_id, $is_app, $menu_category)
    {
        $category_wise_data = array();
        foreach ((count($category_data) > 0 ? $category_data : array()) as $level0) {
            $level0["id"] = intval($level0["id"]);
            $level0["parentId"] = intval($level0["parentId"]);
            $level0["iconImage"] = ($level0["iconImage"] != "" ? (url('images/' . $level0["iconImage"])) : "");

            $userwise_category_notification = UserCategoryNotificationDetail::select("isNotificationOn")->where("userId", $user_id)->where("categoryId", $level0["id"])->first();
            if ($userwise_category_notification) {
                $level0["isNotificationOn"] = intval($userwise_category_notification->isNotificationOn);
            } else {
                $level0["isNotificationOn"] = 1;
            }
            $single_category = $level0;
            unset($single_category[($menu_category ? "nav_menu_index" : "more_section_index")]);

            if ($menu_category) {
                $sub_category_data = CategoryDetail::selectRaw("id,name,iconImage,isActualCategory,parentId,level,isDisplayInMenu,isDisplayInMore,nav_menu_index")->where("isDisplayInApp", $is_app)->where("isDisplayInMenu", 1)->where("level", 1)->where("parentId", $level0["id"])->orderBy("nav_menu_index")->get()->toArray();
            } else {
                $sub_category_data = CategoryDetail::selectRaw("id,name,iconImage,isActualCategory,parentId,level,isDisplayInMenu,isDisplayInMore,more_section_index")->where("isDisplayInApp", $is_app)->where("isDisplayInMore", 1)->where("level", 1)->where("parentId", $level0["id"])->orderBy("more_section_index")->get()->toArray();
            }

            $sub_category_array = array();
            foreach (count($sub_category_data) > 0 ? $sub_category_data : array() as $s) {
                $s["id"] = intval($s["id"]);
                $s["iconImage"] = ($s["iconImage"] != "" ? (url('images/' . $s["iconImage"])) : "");
                $userwise_category_notification = UserCategoryNotificationDetail::select("isNotificationOn")->where("userId", $user_id)->where("categoryId", $s["id"])->first();
                if ($userwise_category_notification) {
                    $s["isNotificationOn"] = intval($userwise_category_notification->isNotificationOn);
                } else {
                    $s["isNotificationOn"] = 1;
                }
                $s["parentId"] = intval($s["parentId"]);
                unset($s[($menu_category ? "nav_menu_index" : "more_section_index")]);
                $s["childList"] = array();
                array_push($sub_category_array, $s);
            }
            $single_category["childList"] = ($sub_category_array) ? $sub_category_array : array();
            array_push($category_wise_data, $single_category);
        }
        return $category_wise_data;
    }

    /**
     *  Method to get category wise article data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryWiseArticleData(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $category_id = $request->input("category_id");
            if ($category_id == "") {
                return $this->sendResultJSON("0", "Category id not found");
            }
            $ad_display_index = config("constants.advertisement_index");
            $page = (int)request("active_page");
            $top_record_count = request("top_article_count") ? (int)request("top_article_count") : 5;
            $total_records = (int)request("total_records");
            $id_not_display = (request("ad_ids") ? explode(",", request("ad_ids")) : array());

            $start_index = (($page * $total_records) - $total_records) + 1;
            $end_index = ($page * $total_records);
            $ad_index_array = array();
            $article_array = array();
            while ($start_index <= $end_index) {
                $article_array[$start_index] = array();
                if ($start_index % $ad_display_index == 0) {
                    array_push($ad_index_array, $start_index);
                }
                $start_index++;
            }

            $top_article_details = array();
            $category_id_array = array();
            $category_data = CategoryDetail::select("name", "parentId")->where("id", $category_id)->where("isDisplayInApp", 1)->first();
            if ($category_data) {
                // if ($category_data->name == "Latest News") {
                //     $category_id_array = $this->getSubCategoryFromParent($category_data->parentId);
                // } else {
                array_push($category_id_array, $category_id);
                //}
            }
            $id_to_skip = array();
            $top_article_data = ArticleDetail::whereIn("categoryId", $category_id_array)->where("status", 1)->where("isVideo", 0)->take($top_record_count)->orderBy("publishDate", "desc")->get();
            foreach (count((array)$top_article_data) > 0 ? $top_article_data : array() as $t) {
                if ($page == 1) {
                    array_push($top_article_details, $this->itereateArticleData($t));
                }
                array_push($id_to_skip, $t->id);
            }

            $article_data = ArticleDetail::whereIn("categoryId", $category_id_array)->where("status", 1)->where("isVideo", 0)->whereNotIn("id", $id_to_skip);
            $total_article_data = $article_data->count();

            $skip = ((($page - 1) * $total_records) - count($id_not_display));
            $take = $total_records - count($ad_index_array);
            $article_data = $article_data->skip($skip)->take($take)->orderBy("publishDate", "desc")->get();

            $start_index = (($page * $total_records) - $total_records) + 1;
            foreach (count((array)$article_data) > 0 ? $article_data : array() as $a) {
                if (!in_array($start_index, $ad_index_array)) {
                    $article_array[$start_index] = $this->itereateArticleData($a, $user_id);
                } else {
                    $start_index++;
                    $article_array[$start_index] = $this->itereateArticleData($a, $user_id);
                }
                $start_index++;
            }
            $banner_images = array();
            $parent_category_id = (isset($category_data->parentCategoryDetail) ? $category_data->parentCategoryDetail->id : $category_id);
            if ((count((array)$article_data) > 0) && ((count((array)$article_data) + count($ad_index_array)) > $ad_display_index)) {
                foreach ($ad_index_array as $i) {
                    $ad_data = $this->getAdvertisementData(1, $parent_category_id, $id_not_display, "banner");
                    if (count($ad_data) > 0) {
                        foreach ($ad_data as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $article_array[$i] = $ad;
                        }
                    } else {
                        $ad_data = $this->getAdvertisementData(1, $parent_category_id, array(), "banner");
                        foreach ($ad_data as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $article_array[$i] = $ad;
                        }
                    }
                }
            }
            if (count((array)$article_data) > 0) {
                $banner_images = $this->getAdvertisementData("", $parent_category_id, array(), "banner");
            }

            $article_array = array_values(array_filter($article_array));
            return $this->sendResultJSON("1", (count((array)$article_array) > 0 ? "" : "No article(s) found."), array("total_records" => $total_article_data, "top_five_articles" => $top_article_details, "article_data" => $article_array, "advertisement_ids" => $id_not_display, "advertisement_data" => array("square" => array(), "banner" => $banner_images)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get category wise article data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeaturedArticles()
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $page = (int)request("active_page");
            $top_record_count = request("top_article_count") ? (int)request("top_article_count") : 5;
            $total_records = (int)request("total_records");
            $id_not_display = (request("ad_ids") ? explode(",", request("ad_ids")) : array());

            $ad_display_index = config("constants.advertisement_index");
            $start_index = (($page * $total_records) - $total_records) + 1;
            $end_index = ($page * $total_records);
            $ad_index_array = array();
            $article_data = array();
            while ($start_index <= $end_index) {
                $article_data[$start_index] = array();
                if ($start_index % $ad_display_index == 0) {
                    array_push($ad_index_array, $start_index);
                }
                $start_index++;
            }

            $user_id = session("user_details")->id;
            $get_feature_id = get_feature_id("Featured");

            $featured_category_data = CategoryDetail::select("id")->where("name", "Top Stories")->where("level", 0)->first();
            $category_id = "";
            if ($featured_category_data) {
                $category_id = $featured_category_data->id;
            }

            $top_article_details = array();
            $id_to_skip = array();
            $top_article_data = ArticleDetail::whereRaw("FIND_IN_SET($get_feature_id,featureId) > 0")->where("isVideo", 0)->where("status", 1)->take($top_record_count)->orderBy("publishDate", "desc")->get();
            foreach (count((array)$top_article_data) > 0 ? $top_article_data : array() as $t) {
                if ($page == 1) {
                    array_push($top_article_details, $this->itereateArticleData($t, $user_id));
                }
                array_push($id_to_skip, $t->id);
            }

            $featured_article_data = ArticleDetail::whereRaw("FIND_IN_SET($get_feature_id,featureId) > 0")->where("isVideo", 0)->where("status", 1)->whereNotIn("id", $id_to_skip);
            $total_featured_article = $featured_article_data->count();

            $skip = ((($page - 1) * $total_records) - count($id_not_display));
            $take = $total_records - count($ad_index_array);
            $featured_article_data = $featured_article_data->skip($skip)->take($take)->orderBy("publishDate", "desc")->get();

            $start_index = (($page * $total_records) - $total_records) + 1;
            foreach (count((array)$featured_article_data) > 0 ? $featured_article_data : array() as $a) {
                if (!in_array($start_index, $ad_index_array)) {
                    $article_data[$start_index] = $this->itereateArticleData($a, $user_id);
                } else {
                    $start_index++;
                    $article_data[$start_index] = $this->itereateArticleData($a, $user_id);
                }
                $start_index++;
            }
            $square_images = array();
            $banner_images = array();
            if ((count((array)$featured_article_data) > 0) && ((count((array)$featured_article_data) + count($ad_index_array)) > $ad_display_index)) {
                foreach ($ad_index_array as $i) {
                    $ad_data = $this->getAdvertisementData(1, "", $id_not_display, (($i / $ad_display_index) % 2 == 0 ? "banner" : "square"));
                    if (count($ad_data) > 0) {
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $article_data[$i] = $ad;
                        }
                    } else {
                        $ad_data = $this->getAdvertisementData(1, "", array(), (($i / $ad_display_index) % 2 == 0 ? "banner" : "square"));
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $article_data[$i] = $ad;
                        }
                    }
                }
            }
            if (count((array)$featured_article_data) > 0) {
                $square_images = $this->getAdvertisementData("", "", array(), "square");
                $banner_images = $this->getAdvertisementData("", "", array(), "banner");
            }
            $article_data = array_values(array_filter($article_data));
            return $this->sendResultJSON("1", (count($article_data) > 0 ? "" : "No article(s) found."), array("total_records" => $total_featured_article, "top_five_articles" => $top_article_details, "featured_articles" => $article_data, "advertisement_ids" => $id_not_display, "advertisement_data" => array("square" => $square_images, "banner" => $banner_images)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get article data from id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArticleByID($article_id)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            if ($article_id == "") {
                return $this->sendResultJSON("0", "Article id not found");
            }
            $article_data = ArticleDetail::where("id", $article_id)->where("status", 1)->first();
            if ($article_data) {
                $data = UserwiseArticleReadDetail::where("userId", $user_id)->where("articleId", $article_id)->first();
                if (!$data) {
                    $data = new UserwiseArticleReadDetail();
                    $data->userId = $user_id;
                    $data->articleId = $article_id;
                }
                $data->readStatus = 1;
                $data->dateTime = Carbon::now()->format("Y-m-d H:i:s");
                $data->save();

                $category_id = $article_data->categoryId;
                $parent_category_id = $category_id;
                $category_data = CategoryDetail::select("name", "parentId")->where("id", $category_id)->where("isDisplayInApp", 1)->first();
                if ($category_data && isset($category_data->parentCategoryDetail)) {
                    $parent_category_id = $category_data->parentCategoryDetail->id;
                }
                $total_records = (request("total_records") ? (int)request("total_records") : 10);

                $catwise_article_data = ArticleDetail::where("id", "!=", $article_id)->where("categoryId", $category_id)->where("status", 1)->take($total_records)->orderBy("publishDate", "desc")->get();
                $catwise_article_array = array();
                foreach (count((array)$catwise_article_data) > 0 ? $catwise_article_data : array() as $a) {
                    array_push($catwise_article_array, $this->itereateArticleData($a, $user_id));
                }
                $article_detail = $this->itereateArticleData($article_data, $user_id, 1);
                $square_ads = $this->getAdvertisementData("", $parent_category_id, array(), "square");
                $banner_ads = $this->getAdvertisementData("", $parent_category_id, array(), "banner");

                return $this->sendResultJSON("1", "", array("article_data" => $article_detail, "category_wise_article" => $catwise_article_array, "latest_video" => $this->getLatestVideos(), "advertisement_data" => array("square" => $square_ads, "banner" => $banner_ads)));
            }
            return $this->sendResultJSON("0", "Article data not found");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get article data from id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function itereateArticleData($article_data, $user_id = null, $is_detail = 0)
    {
        $description = preg_replace('/<style[\s\S]+?style>/', '', $article_data->description);
        $description = preg_replace('/\s*style\s*=\s*[\"\'][^\"|\']*[\"\']/', '$1', $description);
        $description = preg_replace('/\s*face\s*=\s*[\"\'][^\"|\']*[\"\']/', '$1', $description);

        $doc = new \DOMDocument();
        @$doc->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $description);
        $iframe = $doc->getElementsByTagName('iframe');

        if ($iframe->length > 0) {
            foreach ($iframe as $i) {
                $parent_element = $doc->createElement("div");
                $parent_element->setAttribute("class", "embed-responsive embed-responsive-1by1");

                $i->setAttribute("class", "embed-responsive-item");
                $i->parentNode->replaceChild($parent_element, $i);
                $parent_element->appendChild($i);
            }

            $head = $doc->createElement('head');
            $bs_css = $doc->createElement("link");
            $bs_css->setAttribute("rel", "stylesheet");
            $bs_css->setAttribute("href", "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css");
            $bs_css->setAttribute("integrity", "sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T");
            $bs_css->setAttribute("crossorigin", "anonymous");

            $head->appendChild($bs_css);
            $html = $doc->getElementsByTagName('html');
            foreach ($html as $h) {
                $h->insertBefore($head, $h->firstChild);
            }
            $description = $doc->saveHTML();
        } else {
            $style = "<style type='text/css'>iframe,blockquote{width:100%;}img{width:100%}</style>";
            $description .= $style;
        }
        $description = str_replace("//www.instagram.com/embed.js", "https://www.instagram.com/embed.js", $description);
        $article_detail = array(
            'article_id' => $article_data->id,
            'title' => "",
            'description' => (empty($article_data->description) ? "" : $description),
            'summary' => (empty($article_data->summary) ? "" : $article_data->summary),
            'heading' => (empty($article_data->heading) ? "" : $article_data->heading),
            'author_id' => isset($article_data->authorDetail) ? $article_data->authorDetail->id : "",
            'author_name' => isset($article_data->authorDetail) ? $article_data->authorDetail->name : "",
            'author_image_name' => ($article_data->authorDetail) ? getAuthorImage($article_data->authorDetail->profileImage) : "",
            'publish_date' => (($article_data->publishDate != "") ? Carbon::parse($article_data->publishDate)->format("M d, Y") : ""),
            'landscape_image' => "",
            'image_caption' => "",
            'image_credit' => "",
            'video_file' => "",
            'source' => (empty($article_data->source) ? "" : $article_data->source),
            'is_bookmarked' => 0,
            'is_read' => 0,
            'category_id' => '',
            'category_name' => '',
            'parent_id' => '',
            'parent_name' => '',
            'is_video' => $article_data->isVideo,
            'video_type' => ($article_data->isVideo ? (($article_data->isYoutubeVideo) ? "youtube" : "video") : ""),
            'video_thumbnail_url' => ((!empty($article_data->videoThumbnail)) ? url(("uploads/video_thumbnail/" . $article_data->videoThumbnail)) : ""),
            'video_duration' => (!empty($article_data->videoDuration) ? $article_data->videoDuration : ""),
            'video_credit' => (empty($article_data->videoCredit) ? "" : $article_data->videoCredit),
            'youtube_url' => (empty($article_data->youtubeUrl) ? "" : $article_data->youtubeUrl),
            'video_html' => '',
            'article_type' => '',
            'advertisement_id' => '',
            'is_advertisement' => 0,
            'ad_image_type' => '',
            'ad_image_url' => '',
            'ad_tablet_image_url' => '',
            'ad_link' => '',
            'share_link' => (($article_data->isVideo) ? url('videos', ["$article_data->id", str_slug("$article_data->heading", "-")]) : route('article-detail', ["$article_data->id", str_slug("$article_data->heading", "-")])),
            'is_userzone_ad' => 0
        );
        if ($article_data->isVideo) {
            $article_detail['video_file'] = ((!empty($article_data->videoFile)) ? url(("uploads/video/" . $article_data->videoFile)) : "");
        } else {
            $main_media_detail = $article_data->articleFileDetail->where("isMain", 1);
            foreach (count((array)$main_media_detail) > 0 ? $main_media_detail : array() as $m) {
                if ($m->type == "image") {
                    $article_detail['article_type'] = "image_article";
                    $article_detail['landscape_image'] = ((!empty($m->fileName)) ? $this->getArticleImage($m->fileName) : "");
                } else {
                    $article_detail['article_type'] = "video_article";
                    $article_detail['video_file'] = ((!empty($m->fileName)) ? $this->getArticleImage($m->fileName) : "");
                    $article_detail['video_thumbnail_url'] = ((!empty($m->thumbnailImage)) ? url(("uploads/video_thumbnail/" . $m->thumbnailImage)) : "");
                    $article_detail['video_duration'] = $m->duration;
                    $article_detail['video_html'] = $this->generate_video_html($article_detail['video_file'], $article_detail['video_thumbnail_url'], "video");
                }
            }
            $max_caption_length = 0;
            $max_credit_length = 0;
            if ($is_detail) {
                $article_file_details = array();
                foreach ($article_data->articleFileDetail as $f) {
                    $url = "";
                    if ($f->type == "image") {
                        $url = (!empty($f->fileName) ? $this->getArticleImage($f->fileName) : "");
                    } else {
                        $url = (!empty($f->thumbnailImage) ? url("uploads/video_thumbnail/" . $f->thumbnailImage) : "");
                    }
                    array_push($article_file_details, array(
                        "type" => $f->type,
                        "url" => $url,
                        "video_url" => ($f->type == "video" ? (!empty($f->fileName) ? $this->getArticleImage($f->fileName) : "") : ""),
                        "duration" => ($f->type == "video" ? $f->duration : ""),
                        "credit" => (empty($f->credit) ? "" : $f->credit),
                        "caption" => (empty($f->caption) ? "" : $f->caption)
                    ));
                    $caption_length = strlen($f->caption);
                    $credit_length = strlen($f->credit);
                    if ($caption_length > $max_caption_length) {
                        $max_caption_length = $caption_length;
                        $article_detail['max_caption_index'] = count($article_file_details) - 1;
                    }
                    if ($credit_length > $max_credit_length) {
                        $max_credit_length = $credit_length;
                        $article_detail['max_credit_index'] = count($article_file_details) - 1;
                    }
                }
                $article_detail['slider_images'] = $article_file_details;
            }
        }
        if ($article_detail['video_type'] != "") {
            $article_detail['video_html'] = $this->generate_video_html(($article_detail['video_type'] == "video" ? $article_detail['video_file'] : $article_detail['youtube_url']), $article_detail['video_thumbnail_url'], $article_detail['video_type']);
        }
        $updated_at = Carbon::parse($article_data->updated_at);
        if (Carbon::parse($article_data->created_at)->timestamp != $updated_at->timestamp) {
            $article_detail["last_updated"] = "Last Updated: " . $updated_at->format("M d, Y") . " at " . $updated_at->format("h:i a T");
            $article_detail["publish_date"] = $updated_at->format("M d, Y");
        } else {
            $article_detail["last_updated"] = "";
        }
        if (isset($article_data->categoryDetail)) {
            $article_detail["category_id"] = $article_data->categoryDetail->id;
            $article_detail["category_name"] = $article_data->categoryDetail->name;
            if (intval($article_data->categoryDetail->parentId) != 0) {
                $article_detail["parent_id"] = $article_data->categoryDetail->parentCategoryDetail->id;
                $article_detail["parent_name"] = $article_data->categoryDetail->parentCategoryDetail->name;
            } else {
                $article_detail["parent_id"] = 0;
            }
        }
        if ($user_id != null) {
            $article_detail["is_bookmarked"] = $this->getArticleBookmarkStatus($user_id, $article_data->id);
            $article_detail["is_read"] = $this->getArticleReadStatus($article_data->id, $user_id);
        }
        return $article_detail;
    }

    /**
     *  Method to store user details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUserData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "type" => "required",
                "deviceId" => "required",
                "deviceType" => "required"
            ], [
                "type.required" => "Please enter app type",
                "deviceId.required" => "Please enter device token",
                "deviceType.required" => "Please enter device type",
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }

            $device_id = $request->input("deviceId");
            $user_data = AppUserDetail::where("deviceId", $device_id)->first();
            if (!$user_data) {
                $user_data = new AppUserDetail();
            } else {
                $get_category_details = UserCategoryNotificationDetail::where("userId", $user_data->id);
                if ($get_category_details) {
                    $get_category_details->delete();
                }
                $get_bookmark_details = UserwiseArticleBookmarkDetail::where("userId", $user_data->id);
                if ($get_bookmark_details) {
                    $get_bookmark_details->delete();
                }
                $article_read_details = UserwiseArticleReadDetail::where("userId", $user_data->id);
                if ($article_read_details) {
                    $article_read_details->delete();
                }
            }
            $user_data->type = $request->input("type");
            $user_data->device_type = (!empty($request->input("deviceType")) ? $request->input("deviceType") : 1);
            $user_data->deviceId = $request->input("deviceId");
            $user_data->allow_all_notifications = 1;
            $user_data->allow_location_detection = 0;
            $user_data->notificationAlertStatus = 0;
            $user_data->soundStatus = 0;
            $user_data->vibrateStatus = 0;
            $user_data->save();

            $get_all_categories = $this->getSubCategoryFromParent(0);
            foreach (count($get_all_categories) > 0 ? $get_all_categories : array() as $c) {
                $userwise_cat_notification = new UserCategoryNotificationDetail();
                $userwise_cat_notification->userId = $user_data->id;
                $userwise_cat_notification->categoryId = $c;
                $userwise_cat_notification->isNotificationOn = 1;
                $userwise_cat_notification->save();
            }

            $user_token = generate_access_token("app", $user_data->id);
            return $this->sendResultJSON("1", "User details inserted", array("user_id" => $user_data->id, "allow_all_notifications" => $user_data->allow_all_notifications, "location_detection" => $user_data->allow_location_detection, "notification_alert" => $user_data->notificationAlertStatus, "sound" => $user_data->soundStatus, "vibrate" => $user_data->vibrateStatus, "authentication_token" => $user_token, "totalSteps" => ($user_data->stepDetail ? $user_data->stepDetail->totalSteps : 0)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update user location details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserLocation(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $validator = Validator::make($request->all(), [
                "latitude" => "required",
                "longitude" => "required",
            ], [
                "latitude.required" => "Please enter latitude",
                "longitude.required" => "Please enter longitude",
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }

            $user_data = AppUserDetail::where("id", $user_id)->first();
            if ($user_data) {
                $user_data->latitude = $request->input("latitude");
                $user_data->longitude = $request->input("longitude");
                $user_data->save();
                return $this->sendResultJSON("1", "User location updated");
            }
            return $this->sendResultJSON("0", "User not found");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update user token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDeviceToken(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $validator = Validator::make($request->all(), [
                "device_token" => "required",
            ], [
                "device_token.required" => "Please enter device token",
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }

            $user_data = AppUserDetail::where("id", $user_id)->first();
            if ($user_data) {
                $user_data->deviceToken = $request->input("device_token");
                $user_data->save();
                return $this->sendResultJSON("1", "Device token updated");
            }
            return $this->sendResultJSON("0", "User not found");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update userwise category notification setting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCategoryNotificationSetting(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $validator = Validator::make($request->all(), [
                "category_id" => "required",
                "notification_status" => "required",
            ], [
                "category_id.required" => "Please enter category id",
                "notification_status.required" => "Please enter notification status",
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }
            $category_id = (int)$request->input("category_id");
            $notification_status = $request->input("notification_status");
            if ($category_id == 0) {
                $app_user_data = AppUserDetail::where("id", $user_id)->first();
                if ($app_user_data) {
                    $app_user_data->allow_all_notifications = $notification_status;
                    $app_user_data->save();
                }
            }
            $get_sub_categories = $this->getSubCategoryFromParent($category_id);
            if (count($get_sub_categories) > 0) {
                $notification_data = UserCategoryNotificationDetail::where("userId", $user_id)->whereIn("categoryId", $get_sub_categories)->get();
                if (count((array)$notification_data) > 0) {
                    foreach ($notification_data as $n) {
                        $n->isNotificationOn = $notification_status;
                        $n->save();
                    }
                    return $this->sendResultJSON("1", "Notification setting updated");
                } else {
                    foreach ($get_sub_categories as $n) {
                        $category_notification = new UserCategoryNotificationDetail();
                        $category_notification->userId = $user_id;
                        $category_notification->categoryId = $n;
                        $category_notification->isNotificationOn = $notification_status;
                        $category_notification->save();
                    }
                    return $this->sendResultJSON("1", "Notification setting updated");
                }
                return $this->sendResultJSON("0", "User category notification data not found");
            }
            return $this->sendResultJSON("0", "Categories not found");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to register step up user
     *
     */
    public function registerStepUp(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_data = AppUserDetail::where("id", session("user_details")->id)->first();
            if ($user_data->verificationCount >= 2) {
                return $this->sendResultJSON("2", "You have exceeded the limit of verification attempts.Please email us for further process");
            }
            $validator = Validator::make($request->all(), [
                "email" => "required|email|unique:app_user_details,email," . $user_data->id . ",id,deleted_at,NULL",
                "phoneNo" => "required|unique:app_user_details,phoneNo," . $user_data->id . ",id,deleted_at,NULL"
            ], [
                "email.required" => "Please enter email",
                "email.unique" => "User with this email id is exist",
                "phoneNo.required" => "Please enter phone no",
                "phoneNo.unique" => "User with this phone no is exist",
            ]);
            if ($validator->fails()) {
                return $this->sendResultJSON("2", $validator->errors()->first());
            }
            $user_data->name = ucfirst($request->input("name"));
            $user_data->email = $request->input("email");
            $user_data->phoneNo = $request->input("phoneNo");
            $user_data->isVerified = 0;
            $user_data->verificationCode = rand(1111, 9999);
            $verificationcode = $user_data->verificationCode;
            $user_data->increment('verificationCount', 1);
            $user_data->save();

            $file = NULL;
            if (count($_FILES) > 0) {
                $file = $_FILES["file"];
            }
            if ($file != NULL) {
                $destination_path = public_path("uploads/avatar");
                if (!File::exists($destination_path)) {
                    File::makeDirectory($destination_path);
                }
                $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
                $user_data->avatar = $user_data->id . "." . $ext;
                $destination_path = public_path("uploads/avatar") . "/" . $user_data->avatar;
                move_uploaded_file($file["tmp_name"], $destination_path);
                $user_data->save();
            }
            $client = new Tclient(env("TWILIO_SID"), env("TWILIO_AUTH_TOKEN"));
            $user_phone_no = ("+1" . $user_data->phoneNo);
            $client->messages->create($user_phone_no,
                ['from' => env("TWILIO_NUMBER"), 'body' => "Sign up with Step Up Richmond. Please use this verification code to verify your account : " . $verificationcode]);
            // mailnotification::send($user_data, new VerificationCode(array('text' => 'Hi,', 'subtext' => 'Please use the verification code below to verify your account : ', 'subject' => 'Sign up with Step Up Richmond', 'otp' => "Verification code : " . $verificationcode)));
            return $this->sendResultJSON("1", "User Successfully Registered", array("avatar" => getUserAvatar(session("user_details")->avatar)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    public function updateAvatar(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_data = AppUserDetail::where("id", session("user_details")->id)->first();
            if ($request->input("name")) {
                $user_data->name = ucfirst($request->input("name"));
                $user_data->save();
            }
            $file = NULL;
            if (count($_FILES) > 0) {
                $file = $_FILES["file"];
            }
            if ($file != NULL) {
                $destination_path = public_path("uploads/avatar");
                if (!File::exists($destination_path)) {
                    File::makeDirectory($destination_path);
                }
                $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
                $user_data->avatar = $user_data->id . "." . $ext;
                $destination_path = public_path("uploads/avatar") . "/" . $user_data->avatar;
                move_uploaded_file($file["tmp_name"], $destination_path);
                $user_data->save();
            }
            if ($request->input("isAvatarDeleted")) {
                if ($user_data->avatar != NULL) {
                    unlink(public_path("uploads/avatar/" . $user_data->avatar));
                }
                $user_data->avatar = NULL;
                $user_data->save();
            }
            return $this->sendResultJSON("1", "Avatar updated successfully", array("avatar" => getUserAvatar($user_data->avatar)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to verify verification code
     *
     */
    public function verifyVerificationCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "verificationCode" => "required",
                "email" => "required|email",
            ], [
                "verificationCode.required" => "Please enter verification code",
                "email.required" => "Please enter email",
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("2", $validator->errors()->first());
            }
            $verification_code = $request->input("verificationCode");
            $user_detail = AppUserDetail::where("verificationCode", $verification_code)->where("email", $request->input("email"))->where("isVerified", 0)->first();
            if (count((array)$user_detail) == 0) {
                return $this->sendResultJSON("2", "Invalid verification code");
            }
            $user_detail->isVerified = 1;
            $user_detail->verificationCode = $verification_code;
            $user_detail->save();

            $steps_data = UserwiseStepsDetail::where("userId", $user_detail->id)->first();
            if (!$steps_data) {
                $steps_data = new UserwiseStepsDetail();
                $steps_data->userId = $user_detail->id;
            }
            $steps_data->totalSteps = 0;
            $steps_data->save();

            return $this->sendResultJSON("1", "User verified");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to resend verification code
     *
     */
    public function ResendVerificationCode(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $validator = Validator::make($request->all(), [
                "email" => "required|email"
            ], [
                "email.required" => "Please enter email"
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("2", $validator->errors()->first());
            }
            $user_detail = AppUserDetail::where("id", session("user_details")->id)->first();
            if ($user_detail->isVerified == 1) {
                return $this->sendResultJSON("2", "User is already verified");
            }
            if ($user_detail->verificationCount >= 2) {
                return $this->sendResultJSON("2", "You have exceeded the limit of verification attempts.Please email us for further process");
            }
            $verification_code = rand(1111, 9999);
            if ($user_detail->verificationCode == $verification_code) {
                $verification_code = rand(1111, 9999);
            }
            $user_detail->verificationCode = $verification_code;
            $user_detail->increment('verificationCount', 1);
            $user_detail->save();

            $client = new Tclient(env("TWILIO_SID"), env("TWILIO_AUTH_TOKEN"));
            $user_phone_no = ("+1" . $user_detail->phoneNo);
            $client->messages->create($user_phone_no,
                ['from' => env("TWILIO_NUMBER"), 'body' => "Sign up with Step Up Richmond. Please use this verification code to verify your account : " . $verification_code]);
            // mailnotification::send($user_detail, new VerificationCode(array('text' => 'Hi,', 'subtext' => 'Please use the verification code below to verify your account : ', 'subject' => 'Sign up with Step Up Richmond', 'otp' => "Verification code : " . $verification_code)));
            return $this->sendResultJSON("1", "Verification code is resent. Please check your phone number.");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update userwise category notification setting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function getSubCategoryFromParent($category_id)
    {
        $category_ids = array();
        if ($category_id == 0) {
            $category_ids_data = CategoryDetail::selectRaw("GROUP_CONCAT(id) as cat_ids")->where("isDisplayInApp", 1)->first();

            if ($category_ids_data && $category_ids_data->cat_ids != "") {
                $category_ids = explode(",", $category_ids_data->cat_ids);
                return $category_ids;
            }
        }
        $category_data = CategoryDetail::select("id")->where("isDisplayInApp", 1)->where("id", $category_id)->get();
        foreach (count((array)$category_data) > 0 ? $category_data : array() as $c) {
            array_push($category_ids, $c->id);
            $sub_category_data = CategoryDetail::select("id")->where("isDisplayInApp", 1)->whereIn("level", array(0, 1))->where("parentId", $c->id)->get();
            foreach (count((array)$sub_category_data) > 0 ? $sub_category_data : array() as $sc) {
                array_push($category_ids, $sc->id);
                $last_sub_category_data = CategoryDetail::select("id")->where("isDisplayInApp", 1)->whereIn("level", array(0, 1))->where("parentId", $sc->id)->get();
                foreach (count((array)$last_sub_category_data) > 0 ? $last_sub_category_data : array() as $lc) {
                    array_push($category_ids, $lc->id);
                }
            }
        }
        return $category_ids;
    }

    /**
     *  Method to update user token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLocationDetection(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $validator = Validator::make($request->all(), [
                "location_detection" => "required",
            ], [
                "location_detection.required" => "Please enter location detection status",
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }

            $user_data = AppUserDetail::where("id", $user_id)->first();
            if ($user_data) {
                $user_data->allow_location_detection = $request->input("location_detection");
                $user_data->save();
                return $this->sendResultJSON("1", "Location detection status updated");
            }
            return $this->sendResultJSON("0", "User not found");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update notification alert setting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotificationSoundSetting(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $validator = Validator::make($request->all(), [
                "type" => "required",
                "status" => "required",
            ], [
                "type.required" => "Please enter alert type",
                "status.required" => "Please enter alert status",
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }
            $type = $request->input("type");
            $status = $request->input("status");
            $user_data = AppUserDetail::where("id", $user_id)->first();
            if ($user_data) {
                if ($type == "notification_alert") {
                    $user_data->notificationAlertStatus = $status;
                    $user_data->soundStatus = $status;
                    $user_data->vibrateStatus = $status;
                } else if ($type == "sound") {
                    $user_data->soundStatus = $status;
                } else if ($type == "vibrate") {
                    $user_data->vibrateStatus = $status;
                }
                $user_data->save();
                return $this->sendResultJSON("1", ($type == "notification_alert" ? "Notification alert" : ($type == "sound" ? "Notification sound" : "Notification vibration")) . " updated");
            }
            return $this->sendResultJSON("0", "User not found");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get image of category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImageOfCategory($category_id)
    {
        try {
            if ($category_id == "") {
                return $this->sendResultJSON("0", "Category id not found");
            }
            $category_data = CategoryDetail::selectRaw("iconImage,name")->where("id", $category_id)->first();
            if ($category_data) {
                return '<img src= "' . ($category_data->iconImage != "" ? (url('images/' . $category_data->iconImage)) : "") . '" alt="' . $category_data->name . '"/>';
            }
            return $this->sendResultJSON("0", "Category image not found");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get image of category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArticleImage($image_name)
    {
        return (url('uploads/article') . "/" . $image_name);
    }

    /**
     *  Method to set user article bookmark status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserArticleBookmark(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $validator = Validator::make($request->all(), [
                "article_id" => "required",
                "status" => "required",
            ], [
                "article_id.required" => "Please enter article id",
                "status.required" => "Please enter bookmark status",
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }
            $article_id = $request->input("article_id");
            $type = "Article";
            $article_data = ArticleDetail::selectRaw("isVideo")->where("id", $article_id)->where("status", 1)->first();
            if ($article_data) {
                $type = ($article_data->isVideo ? "Video" : $type);
            }
            $data = UserwiseArticleBookmarkDetail::where("userId", $user_id)->where("articleId", $article_id)->first();
            if (!$data) {
                $data = new UserwiseArticleBookmarkDetail();
                $data->userId = $user_id;
                $data->articleId = $article_id;
            }
            $bookmark_status = intval($request->input("status"));
            $data->bookmarkStatus = $bookmark_status;
            $data->dateTime = Carbon::now()->format("Y-m-d H:i:s");
            $data->save();
            return $this->sendResultJSON("1", ($bookmark_status ? "$type is bookmarked." : "$type is removed from bookmarks."));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to set user article bookmark status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserArticleBookmark(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $page = (int)request("active_page");
            $total_records_to_fetch = (int)request("total_records");

            $category_ids = request("category_ids");
            $author_ids = request("author_ids");
            $start_date = request("start_date");
            $end_date = request("end_date");
            $sort_by = request("sort_column");
            $ad_display_index = config("constants.advertisement_index");
            $id_not_display = (request("ad_ids") ? explode(",", request("ad_ids")) : array());
            $is_video = (request("is_video") ? request("is_video") : 0);

            $start_index = (($page * $total_records_to_fetch) - $total_records_to_fetch) + 1;
            $end_index = ($page * $total_records_to_fetch);
            $ad_index_array = array();
            $article_detail = array();
            while ($start_index <= $end_index) {
                $article_detail[$start_index] = array();
                if ($start_index % $ad_display_index == 0) {
                    array_push($ad_index_array, $start_index);
                }
                $start_index++;
            }

            $data = UserwiseArticleBookmarkDetail::leftJoin("article_details", function ($join) {
                $join->on("userwise_article_bookmark_details.articleId", "=", "article_details.id");
            })->where("userwise_article_bookmark_details.userId", $user_id)->where("userwise_article_bookmark_details.bookmarkStatus", 1);
            $search_string = array();
            if ($category_ids != "" && !$is_video) {
                array_push($search_string, 'article_details.categoryId IN (' . $category_ids . ')');
            }
            if ($author_ids != "") {
                array_push($search_string, 'article_details.authorId IN (' . $author_ids . ')');
            }
            if ($start_date != "" && $end_date != "") {
                array_push($search_string, "(article_details.publishDate >= '$start_date' AND article_details.publishDate <= '$end_date')");
            }
            $where_str = (count($search_string) > 0 ? (implode(" and ", $search_string) . ($is_video ? " AND isVideo = 1" : "")) : ($is_video ? "article_details.isVideo = 1" : ""));

            if ($where_str != "") {
                $data = $data->whereRaw($where_str);
            }
            if ($sort_by != "") {
                if ($sort_by == "DateDesc") {
                    $data = $data->orderBy("article_details.publishDate", "desc");
                } else if ($sort_by == "DateAsc") {
                    $data = $data->orderBy("article_details.publishDate", "asc");
                } else if ($sort_by == "A-Z") {
                    $data = $data->orderBy("article_details.heading", "asc");
                } else if ($sort_by == "Z-A") {
                    $data = $data->orderBy("article_details.heading", "desc");
                } else if ($sort_by == "Featured") {
                    $get_feature_id = get_feature_id($sort_by);
                    $data = $data->orderByRaw('FIND_IN_SET(' . $get_feature_id . ',article_details.featureId) > 0 DESC');
                } else if ($sort_by == "Popular") {
                    $data = $data->orderBy("article_details.viewCount", "desc");
                } else if ($sort_by == "Trending") {
                    $data = $data->orderByRaw("article_details.created_at >= '" . Carbon::now()->subWeeks(1)->format("Y-m-d H:i:s") . "' DESC")->orderBy('article_details.viewCount', 'desc');
                }
            } else {
                $data = $data->orderBy("article_details.publishDate", "desc");
            }
            $total_records = $data->count();

            $skip = ((($page - 1) * $total_records_to_fetch) - count($id_not_display));
            $take = $total_records_to_fetch - count($ad_index_array);
            $data = $data->skip($skip)->take($take)->get();

            $start_index = (($page * $total_records_to_fetch) - $total_records_to_fetch) + 1;
            foreach (count((array)$data) > 0 ? $data : array() as $d) {
                $article_data = $d->articleDetail;
                if (!$article_data) {
                    continue;
                }
                $single_article = $this->itereateArticleData($article_data, $user_id);
                $single_article['publish_date'] = Carbon::parse($single_article['publish_date'])->format("M-d-Y");
                if (!in_array($start_index, $ad_index_array)) {
                    $article_detail[$start_index] = $single_article;
                } else {
                    $start_index++;
                    $article_detail[$start_index] = $single_article;
                }
                $start_index++;
            }
            $banner_images = array();
            if (count((array)$data) > 0 && ((count((array)$data) + count($ad_index_array)) > $ad_display_index)) {
                foreach ($ad_index_array as $i) {
                    $ad_data = $this->getAdvertisementData(1, '', $id_not_display, "banner");
                    if (count($ad_data) > 0) {
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $article_detail[$i] = $ad;
                        }
                    } else {
                        $ad_data = $this->getAdvertisementData(1, '', array(), "banner");
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $article_detail[$i] = $ad;
                        }
                    }
                }
            }
            if (count((array)$data) > 0) {
                $banner_images = $this->getAdvertisementData("", "", array(), "banner");
            }
            $article_detail = array_values(array_filter($article_detail));
            return $this->sendResultJSON("1", (count($article_detail) > 0 ? "" : "No bookmarked article(s) found."), array("user_bookmarked_articles" => $article_detail, "total_records" => $total_records, "advertisement_data" => array("square" => array(), "banner" => $banner_images), "advertisement_ids" => $id_not_display));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get article read status
     *
     *
     */
    private function getArticleReadStatus($article_id, $user_id)
    {
        $is_read = 0;
        $read_data = UserwiseArticleReadDetail::select("readStatus")->where("userId", $user_id)->where("articleId", $article_id)->first();
        if ($read_data) {
            $is_read = intval($read_data->readStatus);
        }
        return $is_read;
    }

    /**
     *  Method to get article bookmark status
     *
     *
     */
    private function getArticleBookmarkStatus($user_id, $article_id)
    {
        $is_bookmarked = 0;
        $bookmark_data = UserwiseArticleBookmarkDetail::select("bookmarkStatus")->where("userId", $user_id)->where("articleId", $article_id)->first();
        if ($bookmark_data) {
            $is_bookmarked = intval($bookmark_data->bookmarkStatus);
        }
        return $is_bookmarked;
    }

    /**
     *  Method to mark article as read
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markArticleAsRead(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $validator = Validator::make($request->all(), [
                "article_id" => "required",
                "status" => "required",
            ], [
                "article_id.required" => "Please enter article id",
                "status.required" => "Please enter read status",
            ]);
            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }
            $article_id = $request->input("article_id");
            $data = UserwiseArticleReadDetail::where("userId", $user_id)->where("articleId", $article_id)->first();
            if (!$data) {
                $data = new UserwiseArticleReadDetail();
                $data->userId = $user_id;
                $data->articleId = $article_id;
            }
            $status = (int)$request->input("status");
            $data->readStatus = $status;
            $data->dateTime = Carbon::now()->format("Y-m-d H:i:s");
            $data->save();
            return $this->sendResultJSON("1", "Marked as " . ($status ? "Read." : "Unread."));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update authorised token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAuthorisedToken(Request $request)
    {
        try {
            $user_id = request("user_id");
            if ($user_id == "") {
                return $this->sendResultJSON("0", "Please enter user id");
            }
            $user_data = AppUserDetail::where("id", $user_id)->first();
            if (!$user_data) {
                return $this->sendResultJSON("0", "User not found");
            }
            $token = generate_access_token("app", $user_id);
            return $this->sendResultJSON("1", "", array("authentication_token" => $token));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get all author name
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function getAllAuthorName()
    {
        try {
            $author_data = AuthorDetail::where("status", 1)->get();
            $all_author_array = array();
            foreach (count((array)$author_data) > 0 ? $author_data : array() as $a) {
                array_push($all_author_array, array(
                    'id' => $a->id,
                    'name' => $a->name,
                ));
            }
            return $all_author_array;
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to search article/videos data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchData(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $page = (int)request("active_page");
            $total_records_to_fetch = (int)request("total_records");

            $search_keyword = request("search_keyword");
            $category_ids = request("category_ids");
            $author_ids = request("author_ids");
            $start_date = request("start_date");
            $end_date = request("end_date");
            $sort_by = request("sort_column");
            $ad_display_index = config("constants.advertisement_index");
            $id_not_display = (request("ad_ids") ? explode(",", request("ad_ids")) : array());
            $is_video = (request("is_video") ? request("is_video") : 0);

            $start_index = (($page * $total_records_to_fetch) - $total_records_to_fetch) + 1;
            $end_index = ($page * $total_records_to_fetch);
            $ad_index_array = array();
            $article_detail = array();
            while ($start_index <= $end_index) {
                $article_detail[$start_index] = array();
                if ($start_index % $ad_display_index == 0) {
                    array_push($ad_index_array, $start_index);
                }
                $start_index++;
            }
            $data = ArticleDetail::where("status", 1);
            $search_string = array();
            if ($search_keyword != "") {
                array_push($search_string, '(heading like "%' . $search_keyword . '%" OR summary like "%' . $search_keyword . '%" OR imageCredit like "%' . $search_keyword . '%" OR imageCaption like "%' . $search_keyword . '%" OR source like "%' . $search_keyword . '%" OR metatag like "%' . $search_keyword . '%" OR videoCredit like "%' . $search_keyword . '%")');
            }
            if ($category_ids != "" && !$is_video) {
                array_push($search_string, 'categoryId IN (' . $category_ids . ')');
            }
            if ($author_ids != "") {
                array_push($search_string, 'authorId IN (' . $author_ids . ')');
            }
            if ($start_date != "" && $end_date != "") {
                array_push($search_string, "(publishDate >= '$start_date' AND publishDate <= '$end_date')");
            }
            $where_str = (count($search_string) > 0 ? (implode(" and ", $search_string) . ($is_video ? " AND isVideo = 1" : "")) : ($is_video ? "isVideo = 1" : ""));
            if ($where_str != "") {
                $data = $data->whereRaw($where_str);
            }
            if ($sort_by != "") {
                if ($sort_by == "DateDesc") {
                    $data = $data->orderBy("publishDate", "desc");
                } else if ($sort_by == "DateAsc") {
                    $data = $data->orderBy("publishDate", "asc");
                } else if ($sort_by == "A-Z") {
                    $data = $data->orderBy("heading", "asc");
                } else if ($sort_by == "Z-A") {
                    $data = $data->orderBy("heading", "desc");
                } else if ($sort_by == "Popular") {
                    $data = $data->orderBy("viewCount", "desc");
                } else if ($sort_by == "Featured") {
                    $get_feature_id = get_feature_id($sort_by);
                    $data = $data->orderByRaw('FIND_IN_SET(' . $get_feature_id . ',featureId) > 0 DESC');
                } else if ($sort_by == "Trending") {
                    $data = $data->orderByRaw("created_at >= '" . Carbon::now()->subWeeks(1)->format("Y-m-d H:i:s") . "' DESC")->orderBy('viewCount', 'desc');
                }
            } else {
                $data = $data->orderBy("publishDate", "desc");
            }
            $total_records = $data->count();
            $skip = ((($page - 1) * $total_records_to_fetch) - count($id_not_display));
            $take = $total_records_to_fetch - count($ad_index_array);

            $data = $data->skip($skip)->take($take)->get();
            $start_index = (($page * $total_records_to_fetch) - $total_records_to_fetch) + 1;
            foreach (count((array)$data) > 0 ? $data : array() as $d) {
                $single_article = $this->itereateArticleData($d, $user_id);
                $single_article['publish_date'] = Carbon::parse($single_article['publish_date'])->format("M-d-Y");
                if (!in_array($start_index, $ad_index_array)) {
                    $article_detail[$start_index] = $single_article;
                } else {
                    $start_index++;
                    $article_detail[$start_index] = $single_article;
                }
                $start_index++;
            }
            $banner_images = array();
            if (count((array)$data) > 0 && ((count((array)$data) + count($ad_index_array)) > $ad_display_index)) {
                foreach ($ad_index_array as $i) {
                    $ad_data = $this->getAdvertisementData(1, '', $id_not_display, "banner");
                    if (count($ad_data) > 0) {
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $article_detail[$i] = $ad;
                        }
                    } else {
                        $ad_data = $this->getAdvertisementData(1, '', array(), "banner");
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $article_detail[$i] = $ad;
                        }
                    }
                }
            }
            if (count((array)$data) > 0) {
                $banner_images = $this->getAdvertisementData("", "", array(), "banner");
            }
            $article_detail = array_values(array_filter($article_detail));

            return $this->sendResultJSON("1", (count($article_detail) > 0 ? "" : "No matching result found."), array("article_data" => $article_detail, "total_records" => $total_records, "advertisement_ids" => $id_not_display, "advertisement_data" => array("square" => array(), "banner" => $banner_images)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to search coupon/business/event data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchAllData(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $page = (int)request("active_page");
            $total_records_to_fetch = (int)request("total_records");
            $search_keyword = request("search_keyword");
            $type = request("type");
            $result_data = $data = array();

            if ($type == "coupon") {
                $data = CouponDetail::where("status", 1);
                if ($search_keyword != "") {
                    $data = $data->whereRaw('(heading like "%' . $search_keyword . '%" OR highlights like "%' . $search_keyword . '%" OR finePrints like "%' . $search_keyword . '%" OR detail like "%' . $search_keyword . '%" OR companyName like "%' . $search_keyword . '%" OR companyAddress like "%' . $search_keyword . '%" OR originalPrice like "%' . $search_keyword . '%" OR discountPrice like "%' . $search_keyword . '%" OR offerDetail like "%' . $search_keyword . '%")')->orderBy("id", "desc");
                }
            } else if ($type == "directory") {
                $data = BusinessDetail::selectRaw("*");
                if ($search_keyword != "") {
                    $data = $data->whereRaw('(name like "%' . $search_keyword . '%" OR address1 like "%' . $search_keyword . '%" OR address2 like "%' . $search_keyword . '%" OR city like "%' . $search_keyword . '%" OR province like "%' . $search_keyword . '%" OR postalCode like "%' . $search_keyword . '%" OR country like "%' . $search_keyword . '%" OR about like "%' . $search_keyword . '%" OR email like "%' . $search_keyword . '%" OR phone like "%' . $search_keyword . '%" OR website like "%' . $search_keyword . '%")')->orderBy("id", "desc");
                }
            } else if ($type == "event") {
                $data = EventDetail::selectRaw("*");
                if ($search_keyword != "") {
                    $data = $data->whereRaw('(name like "%' . $search_keyword . '%" OR description like "%' . $search_keyword . '%" OR eventDate like "%' . $search_keyword . '%" OR eventTime like "%' . $search_keyword . '%" OR venue like "%' . $search_keyword . '%" OR venueAddress like "%' . $search_keyword . '%" OR venuePhone like "%' . $search_keyword . '%" OR organizerName like "%' . $search_keyword . '%")')->orderBy("eventDate", "asc");
                }
            }
            $total_records = $data->count();
            $skip = (($page - 1) * $total_records_to_fetch);
            if ($type != "event")
                $data = $data->skip($skip)->take($total_records_to_fetch)->get();
            else
                $data = $data->get();
            if (!$data->isEmpty()) {
                foreach ($data as $d) {
                    if ($type == "coupon") {
                        $less = $d->originalPrice - $d->discountPrice;
                        $discount_price = (100 * $less) / $d->originalPrice;
                        $offerDetail = (empty($d->offerDetail) ? (round($discount_price) . "% OFF") : $d->offerDetail);
                        array_push($result_data, array("id" => $d->id, "name" => $d->name, "heading" => $d->heading, "companyName" => $d->companyName, "thumbnailImage" => url($d->thumbnailImage), "originalPrice" => "C$" . $d->originalPrice, "discountPrice" => "C$" . $d->discountPrice, "offerDetail" => $offerDetail));
                    } else if ($type == "directory") {
                        array_push($result_data, array("id" => $d->id, "name" => $d->name, "logo" => getBusinessImage($d->logo), "address" => getBusinessAddress($d), "about" => $d->about, "phone" => $d->phone, "email" => $d->email, "is_featured" => intval($d->isFeatured), "website" => $d->website));
                    } else if ($type == "event") {
                        if (!isset($result_data[$d->eventDate])) {
                            $result_data[$d->eventDate] = array("date" => $d->eventDate, "data" => array());
                        }
                        array_push($result_data[$d->eventDate]["data"], getEventData($d));
                    }
                }
            }
            if ($type == "event") {
                $result_data = array_values(array_slice($result_data, $skip, $total_records_to_fetch));
            }
            return $this->sendResultJSON("1", (count($result_data) > 0 ? "" : "No matching result found."), array("data" => $result_data, "total_records" => $total_records));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get video data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVideoData()
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $page = (int)request("active_page");
            $total_records = (int)request("total_records");
            $user_id = session("user_details")->id;

            $id_not_display = (request("ad_ids") ? explode(",", request("ad_ids")) : array());
            $ad_display_index = config("constants.advertisement_index");
            $start_index = (($page * $total_records) - $total_records) + 1;
            $end_index = ($page * $total_records);
            $ad_index_array = array();
            $all_vidoes_data = array();
            while ($start_index <= $end_index) {
                $all_vidoes_data[$start_index] = array();
                if ($start_index % $ad_display_index == 0) {
                    array_push($ad_index_array, $start_index);
                }
                $start_index++;
            }

            $video_category_data = CategoryDetail::select("id")->where("name", "Videos")->where("level", 0)->first();
            $category_id = "";
            if ($video_category_data) {
                $category_id = $video_category_data->id;
            }
            $video_data = ArticleDetail::where("isVideo", 1)->where("status", 1);
            $total_videos = $video_data->count();

            $skip = ((($page - 1) * $total_records) - count($id_not_display));
            $take = $total_records - count($ad_index_array);
            $video_data = $video_data->skip($skip)->take($take)->orderBy("publishDate", "desc")->get();

            $start_index = (($page * $total_records) - $total_records) + 1;
            foreach (count((array)$video_data) > 0 ? $video_data : array() as $v) {
                if (!in_array($start_index, $ad_index_array)) {
                    $all_vidoes_data[$start_index] = $this->itereateArticleData($v, $user_id);
                } else {
                    $start_index++;
                    $all_vidoes_data[$start_index] = $this->itereateArticleData($v, $user_id);
                }
                $start_index++;
            }
            $square_images = array();
            $banner_images = array();
            if (count((array)$video_data) > 0 && ((count((array)$video_data) + count($ad_index_array)) > $ad_display_index)) {
                foreach ($ad_index_array as $i) {
                    $ad_data = $this->getAdvertisementData(1, $category_id, $id_not_display, "square");
                    if (count($ad_data) > 0) {
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $all_vidoes_data[$i] = $ad;
                        }
                    } else {
                        $ad_data = $this->getAdvertisementData(1, $category_id, array(), "square");
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $all_vidoes_data[$i] = $ad;
                        }
                    }
                }
            }
            if (count((array)$video_data) > 0) {
                $square_images = $this->getAdvertisementData("", $category_id, array(), "square");
            }
            $all_vidoes_data = array_values(array_filter($all_vidoes_data));

            return $this->sendResultJSON("1", (count($all_vidoes_data) > 0 ? "" : "No video(s) found."), array("video_data" => $all_vidoes_data, "total_records" => $total_videos, "advertisement_ids" => $id_not_display, "advertisement_data" => array("square" => $square_images, "banner" => $banner_images)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    private function generate_video_html($video_url, $poster_url, $video_type)
    {
        $style = "overflow: hidden; overflow-x: hidden; overflow-y: hidden; height: 0; max-height: 100%; max-width: 100%; min-height: 100%; min-width: 100%; width: 0;scrolling:no;position:absolute;top:0px;left:0px;right:0px;bottom:0px";

        if ($video_type == "video") {
            return "<video style='$style' controls autoplay poster='" . $poster_url . "' preload='auto'><source src='" . $video_url . "'>Your browser does not support the video tag.</video>";
        } else {
            return "<iframe src='https://www.youtube.com/embed/$video_url?&showinfo=0' style='$style' frameborder='0' allowfullscreen></iframe>";
        }
    }

    /**
     *  Method to get article data from id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVideoByID(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $article_id = $request->input("video_id");
            if ($article_id == "") {
                return $this->sendResultJSON("0", "Video id not found");
            }
            $page = (int)request("active_page");
            $total_records = (int)request("total_records");

            $id_not_display = (request("ad_ids") ? explode(",", request("ad_ids")) : array());
            $ad_display_index = config("constants.advertisement_index");
            $start_index = (($page * $total_records) - $total_records) + 1;
            $end_index = ($page * $total_records);
            $ad_index_array = array();
            $all_vidoes_data = array();
            while ($start_index <= $end_index) {
                $all_vidoes_data[$start_index] = array();
                if ($start_index % $ad_display_index == 0) {
                    array_push($ad_index_array, $start_index);
                }
                $start_index++;
            }

            $video_category_data = CategoryDetail::select("id")->where("name", "Videos")->where("level", 0)->first();
            $category_id = "";
            if ($video_category_data) {
                $category_id = $video_category_data->id;
            }
            $video_data = ArticleDetail::where("id", "!=", $article_id)->where("isVideo", 1)->where("status", 1);
            $total_videos = $video_data->count();

            $skip = ((($page - 1) * $total_records) - count($id_not_display));
            $take = $total_records - count($ad_index_array);
            $video_data = $video_data->skip($skip)->take($take)->orderBy("publishDate", "desc")->get();

            $start_index = (($page * $total_records) - $total_records) + 1;
            foreach (count((array)$video_data) > 0 ? $video_data : array() as $v) {
                if (!in_array($start_index, $ad_index_array)) {
                    $all_vidoes_data[$start_index] = $this->itereateArticleData($v, $user_id);
                } else {
                    $start_index++;
                    $all_vidoes_data[$start_index] = $this->itereateArticleData($v, $user_id);
                }
                $start_index++;
            }
            $square_images = array();
            $banner_images = array();
            if (count((array)$video_data) > 0 && ((count((array)$video_data) + count($ad_index_array)) > $ad_display_index)) {
                foreach ($ad_index_array as $i) {
                    $ad_data = $this->getAdvertisementData(1, $category_id, $id_not_display, "banner");
                    if (count($ad_data) > 0) {
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $all_vidoes_data[$i] = $ad;
                        }
                    } else {
                        $ad_data = $this->getAdvertisementData(1, $category_id, array(), "banner");
                        foreach (count($ad_data) > 0 ? $ad_data : array() as $ad) {
                            array_push($id_not_display, (string)$ad["advertisement_id"]);
                            $all_vidoes_data[$i] = $ad;
                        }
                    }
                }
            }
            if (count((array)$video_data) > 0) {
                $banner_images = $this->getAdvertisementData("", $category_id, array(), "banner");
            }
            $all_vidoes_data = array_values(array_filter($all_vidoes_data));

            $single_video_data = "";
            $get_video_data = ArticleDetail::where("id", $article_id)->where("status", 1)->first();
            if ($get_video_data) {
                $single_video_data = $this->itereateArticleData($get_video_data, $user_id);
            }
            return $this->sendResultJSON("1", (count($all_vidoes_data) > 0 ? "" : "No video(s) found."), array("video_data" => $all_vidoes_data, "total_records" => $total_videos, "advertisement_ids" => $id_not_display, "advertisement_data" => array("square" => $square_images, "banner" => $banner_images), "video_detail" => $single_video_data));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to send notification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotification(Request $request)
    {
        try {
            $options = [
                'key_id' => '338K9DWA35', // The Key ID obtained from Apple developer account
                'team_id' => '638BW42L7E', // The Team ID obtained from Apple developer account
                'app_bundle_id' => 'com.intellidt.richmondsentinel', // The bundle ID for app obtained from Apple developer account
                'private_key_path' => url("notification") . '/AuthKey_338K9DWA35.p8', // Path to private key
                'private_key_secret' => null, // Private key secret
            ];
            $authProvider = AuthProvider\Token::create($options);
            $user_data = AppUserDetail::where("type", 1)->whereRaw("deviceToken IS NOT NULL")->get();
            $notifications = [];
            $article_id = $request->input("article_id");
            $article_data = ArticleDetail::find($article_id);
            foreach (count((array)$user_data) > 0 ? $user_data : array() as $u) {
                $alert = Alert::create();
                $alert = $alert->setBody($article_data->heading);
                $payload = Payload::create()->setAlert($alert);

                //add custom value to your notification, needs to be customized
                $payload->setCustomValue('id', $article_data->id);
                $payload->setCustomValue('title', $article_data->title);
                $payload->setCustomValue('type', "article");
                if ($u->soundStatus) {
                    //set notification sound to default
                    $payload->setSound('default');
                }
                $notifications[] = new Notification($payload, $u->deviceToken);
            }
            if (count($notifications) > 0) {
                $client = new Client($authProvider, $production = false);
                $client->addNotifications($notifications);
                $client->push();
            }
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to send notification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotificationAndroid(Request $request)
    {
        try {
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
            $deviceTokens = array("dQd-PUP4q2Q:APA91bHsY7l2iEm51tw6bNGoCnpoakI3WXmHKcqS6AwrJwZeAxEQ0W3sQHGboJLZCMfCRONKWWFcn1v_Btcitkie0x2ki4DUu_7rjaixq0zRmbHuTrrJSxiCKtj91lBIVvcU08Unr9kg", "eAmF8HfY9cU:APA91bEwDSSoQkt1fol186M_IqLmuA-hH4kEEV_y-vvyZwPjTuK0tjkj1RNIe6ZWvuL4iRcNu5w7zdhmoHVXoaCwyIoNvFCD95zvoFxRTpHytnpjATYhT5fcB7neOPhzBB4LT-FWCwN4");

            if (count($deviceTokens) > 0) {
                $article_id = $request->input("article_id");
                $article_data = ArticleDetail::find($article_id);
                $notification = [
                    'heading' => $article_data->heading,
                ];

                $extraNotificationData = ["message" => $notification, "moredata" => array("id" => $article_id, "title" => $article_data->categoryDetail->name, "type" => $request->input("type"), "summary" => $article_data->summary)];

                $fcmNotification = [
                    'registration_ids' => $deviceTokens, //multple token array
                    'data' => $extraNotificationData,
                ];

                $headers = [
                    'Authorization: key=AIzaSyDH5z6yV5CpMcmbZlB3-vrg_Lh38aBlgH8',
                    'Content-Type: application/json',
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                $result = curl_exec($ch);
                curl_close($ch);
                echo json_encode($result);
            }
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get latest videos
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestVideos()
    {
        try {
            $user_id = session("user_details")->id;
            $total_records = (request("total_records") ? (int)request("total_records") : 10);
            $latest_video_data = ArticleDetail::where("status", 1)->where("isVideo", 1)->take($total_records)->orderBy("publishDate", "desc")->get();
            $latest_video_array = array();

            foreach (count((array)$latest_video_data) > 0 ? $latest_video_data : array() as $a) {
                $single_article = $this->itereateArticleData($a, $user_id);
                array_push($latest_video_array, $single_article);
            }
            return $latest_video_array;
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get advertisement data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function getAdvertisementData($limit = "", $category_id = "", $id_not_allow = array(), $image_type = "")
    {
        $latitude = 0;
        $longitude = 0;
        $dist = 0.6;
        if (session("user_details")->allow_location_detection) {
            $latitude = session("user_details")->latitude ?? 0;
            $longitude = session("user_details")->longitude ?? 0;
        }
        $distance_query = "3956 * 2 *  ASIN(SQRT( POWER(SIN(($latitude - abs(latitude))*pi()/180/2),2)+COS( $latitude*pi()/180 )*COS(abs(latitude)*pi()/180)*POWER(SIN(($longitude-longitude)*pi()/180/2),2))) as distance";

        $select_query = "SELECT * FROM(SELECT advertisement_details.*,advertisement_file_details.imageName,advertisement_file_details.categoryId,$distance_query from advertisement_details left join advertisement_file_details on advertisement_details.id = advertisement_file_details.advertisementId WHERE advertisement_details.status = 1 and advertisement_file_details.advertisementFor = 'app' and advertisement_details.deleted_at IS NULL";

        if (count($id_not_allow) > 0) {
            $select_query .= " AND advertisement_details.id NOT IN (" . implode(",", $id_not_allow) . ")";
        }
        if ($category_id != "") {
            $select_query .= " AND (FIND_IN_SET($category_id,advertisement_file_details.categoryId)  > 0)";
        }
        if ($image_type == "square") {
            $select_query .= " AND advertisement_file_details.position = 'square'";
        } else if ($image_type == "banner") {
            $select_query .= " AND advertisement_file_details.position = 'horizontal'";
        }

        $select_query .= ") as t";
        if (session("user_details")->allow_location_detection) {
            $select_query .= ' ORDER BY distance < ' . $dist . ' DESC, RAND()';
        } else {
            $select_query .= ' ORDER BY RAND()';
        }
        if ($limit != "") {
            $select_query .= (' LIMIT ' . $limit);
        }
        $data = DB::select($select_query);
        $advertisement_data = array();
        foreach ((count((array)$data) > 0 ? $data : array()) as $d) {
            $individual_ad = array("article_id" => "", "title" => "", "description" => "", "summary" => "", "heading" => "", "author_id" => "", "author_name" => "", "author_image_name" => "", "publish_date" => "", "vertical_image" => "", "landscape_image" => "", "image_caption" => "", "image_credit" => "", "video_file" => "", "source" => "", "is_bookmarked" => "", "is_read" => "", "category_id" => "", "category_name" => "", "parent_id" => "", "parent_name" => "", "is_video" => "", "video_type" => "", "video_duration" => "", "video_thumbnail_url" => "", "video_credit" => "", "youtube_url" => "", "video_html" => "", "article_type" => "", "last_updated" => "", "share_link" => "");
            $individual_ad["advertisement_id"] = $d->id;
            $individual_ad["is_advertisement"] = 1;
            $individual_ad["ad_image_type"] = $image_type;
            $individual_ad["ad_image_url"] = asset("uploads/advertisement") . "/" . $d->imageName;
            $individual_ad["ad_tablet_image_url"] = "";
            if ($image_type == "square") {
                $tablet_image_data = AdvertisementFileDetail::select("imageName")->where("advertisementId", $d->id)->where("position", "tablet_square")->first();
                if ($tablet_image_data) {
                    $individual_ad["ad_tablet_image_url"] = asset("uploads/advertisement") . "/" . $tablet_image_data->imageName;
                }
            }
            $individual_ad["ad_link"] = $d->link;
            $individual_ad["is_userzone_ad"] = (($d->distance != null && $d->distance < $dist) ? 1 : 0);
            array_push($advertisement_data, $individual_ad);
        }
        return $advertisement_data;
    }

    /**
     *  Method to get business detail
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBusinessDetail(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $validator = Validator::make($request->all(), [
                "businessId" => "required"
            ], [
                "businessId.required" => "Please enter business id"
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }
            $business_id = $request->input("businessId");
            $data = BusinessDetail::where("id", $business_id)->first();
            if (!$data) {
                return $this->sendResultJSON("0", "Business data not found");
            }
            $related_articles = $description_articles = $daywise_data_array = $event_array = array();
            $function_data = getRelatedArticles($business_id, $data->name);
            $articles = $function_data["article"];
            $category = $function_data["category"];
            foreach (count($articles) > 0 ? $articles : array() as $a) {
                if (!isset($related_articles[$a->id]) && !isset($description_articles[$a->id])) {
                    $count = 0;
                    foreach (count($category) > 0 ? $category : array() as $c) {
                        if (strpos($a->metaTag, $c) !== false) {
                            $count = $count + 1;
                        }
                    }
                    if ($count > 0) {
                        $related_articles[$a->id] = $this->itereateArticleData($a);
                    } else {
                        $description_articles[$a->id] = $this->itereateArticleData($a);
                    }
                }
            }
            $business_hours = json_decode($data->businessHours);
            foreach ($business_hours as $h => $value) {
                $time_array = array();
                foreach ($value as $v) {
                    array_push($time_array, Carbon::parse($v->start_time)->format("h:i A") . " - " . Carbon::parse($v->end_time)->format("h:i A"));
                }
                array_push($daywise_data_array, array("day" => ucfirst(config("constants.day_name")[$h]), "time_slot" => $time_array));
            }
            $events = EventDetail::where("businessId", $data->id)->get();
            foreach (count((array)$events) > 0 ? $events : array() as $e) {
                array_push($event_array, getEventData($e));
            }
            $image_array = array();
            $business_images = BusinessPhotosDetail::selectRaw("fileName")->where("businessId", $data->id)->get();
            foreach (count((array)$business_images) > 0 ? $business_images : array() as $i) {
                array_push($image_array, (asset("uploads/business") . "/" . $data->id . "/" . $i->fileName));
            }

            return $this->sendResultJSON("1", "", array("business_detail" => array("name" => $data->name, "logo" => getBusinessImage($data->logo), "address" => getBusinessAddress($data), "about" => $data->about, "phone" => $data->phone, "email" => $data->email, "is_featured" => intval($data->isFeatured), "website" => $data->website, "categories" => implode(",", $category), "opening_hours" => $daywise_data_array, "images" => $image_array, "latitude" => $data->latitude, "longitude" => $data->longitude), "related_stories" => array_merge(array_values($related_articles), array_values($description_articles)), "events" => $event_array, "coupons" => $this->getCouponData()));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get event detail
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventDetail(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $validator = Validator::make($request->all(), [
                "eventId" => "required"
            ], [
                "eventId.required" => "Please enter event id"
            ]);

            if ($validator->fails()) {
                return $this->sendResultJSON("0", $validator->errors()->first());
            }
            $event_id = $request->input("eventId");
            $data = EventDetail::where("id", $event_id)->first();
            if (!$data) {
                return $this->sendResultJSON("0", "Event data not found");
            }
            $price = json_decode($data->price);
            $banner_images = $this->getAdvertisementData("", "", array(), "banner");
            if ($data->eventBusiness) {
                $organizor_name = $data->eventBusiness->name;
                $organizor_phone = $data->eventBusiness->phone;
                $organizor_email = $data->eventBusiness->email;
                $organizor_website = $data->eventBusiness->website;
            } else {
                $organizor_name = $data->organizerName;
                $organizor_phone = $data->organizerPhone;
                $organizor_email = $data->organizerEmail;
                $organizor_website = $data->organizerWebsite;
            }
            $user_id = session("user_details")->id;
            $get_feature_id = get_feature_id("Featured");

            $top_article_details = array();
            $top_article_data = ArticleDetail::whereRaw("FIND_IN_SET($get_feature_id,featureId) > 0")->where("isVideo", 0)->where("status", 1)->take(10)->orderBy("publishDate", "desc")->get();
            foreach (count((array)$top_article_data) > 0 ? $top_article_data : array() as $t) {
                array_push($top_article_details, $this->itereateArticleData($t, $user_id));
            }
            return $this->sendResultJSON("1", "", array("event_detail" => array("name" => $data->name, "description" => $data->description, "event_date" => Carbon::parse($data->eventDate)->format("D, M d, Y"), "event_time" => Carbon::parse($data->eventTime)->format("h:i a"), "venue" => $data->venue, "venueAddress" => $data->venueAddress, "venuePhone" => $data->venuePhone, "organizer_name" => $organizor_name, "organizer_phone" => $organizor_phone, "organizer_email" => $organizor_email, "organizer_website" => $organizor_website, "banner_image" => getEventBanner($data->bannerImage), "booking_link" => $data->bookingLink, "price" => config("constants.event_price_option")[$price->option], "cost" => $price->value, "link_text" => $data->linkText, "category_name" => $data->eventCategory->name, "share_link" => url("/")), "advertisement_data" => array("square" => array(), "banner" => $banner_images), "coupons" => $this->getCouponData(), "top_stories" => $top_article_details));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    private function getCouponData()
    {
        $coupons = array();
        $all_coupons = CouponDetail::get()->take(10);
        foreach (count((array)$all_coupons) > 0 ? $all_coupons : array() as $c) {
            $less = $c->originalPrice - $c->discountPrice;
            $discount_price = (100 * $less) / $c->originalPrice;
            $offerDetail = (empty($c->offerDetail) ? (round($discount_price) . "% OFF") : $c->offerDetail);
            array_push($coupons, array("id" => $c->id, "name" => $c->name, "heading" => $c->heading, "companyName" => $c->companyName, "thumbnailImage" => url($c->thumbnailImage), "originalPrice" => "C$" . $c->originalPrice, "discountPrice" => "C$" . $c->discountPrice, "offerDetail" => $offerDetail));
        }
        return $coupons;
    }
}
