<?php

use Alaouy\Youtube\Facades\Youtube;
use App\Models\AdvertisementDetail;
use App\Models\AppUserDetail;
use App\Models\ArticleDetail;
use App\Models\ArticleFeatureDetail;
use App\Models\BusinessCategoryDetail;
use App\Models\CategoryDetail;
use App\Models\UserCategoryNotificationDetail;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Pushok\AuthProvider;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;
use Illuminate\Support\Facades\Input;
use App\Models\NotificationDetail;

function generate_access_token($type, $user_id)
{
    $token = json_encode(array(
        "type" => $type,
        "user_id" => $user_id,
        "timestamp" => Carbon::Now()->timestamp,
    ));
    return base64_encode(base64_encode($token));
}

function get_feature_id($name)
{
    $get_feature_id = ArticleFeatureDetail::select("id")->where("name", $name)->first();
    if ($get_feature_id) {
        return $get_feature_id->id;
    }
    return "0";
}

/**
 * Parse a comma-separated list (or array) of ids from a request into integers,
 * dropping anything non-numeric, so it can be passed safely to whereIn().
 */
function parseIdList($value): array
{
    $values = is_array($value) ? $value : explode(",", (string) $value);
    $values = array_filter(array_map("trim", array_filter($values, "is_scalar")), "is_numeric");

    return array_values(array_map("intval", $values));
}

function get_category_id($name)
{
    $get_category_id = CategoryDetail::select("id")->where("name", $name)->first();
    if ($get_category_id) {
        return $get_category_id->id;
    }
    return "0";
}

function get_category_data()
{
    $category_data_array = array();
    $category_data = CategoryDetail::selectRaw("id,name,parentId,level")->where("level", 0)->where("isDisplayInFrontend", 1)->where("isCouponCategory", 0)->orderBy("frontend_menu_index")->get();
    foreach (count((array)$category_data) > 0 ? $category_data : array() as $c) {
        $sub_category_array = array();
        $sub_category_data = CategoryDetail::selectRaw("id,name,parentId,level")->where("level", 1)->where("parentId", $c->id)->where("isDisplayInFrontend", 1)->where("isCouponCategory", 0)->orderBy("id")->get();
        foreach (count((array)$sub_category_data) > 0 ? $sub_category_data : array() as $sc) {
            $last_category_array = array();
            $last_sub_category_data = CategoryDetail::selectRaw("id,name,parentId,level")->where("level", 2)->where("parentId", $sc->id)->where("isDisplayInFrontend", 1)->where("isCouponCategory", 0)->orderBy("id")->get();
            foreach (count((array)$last_sub_category_data) > 0 ? $last_sub_category_data : array() as $lc) {
                $last_category_array[$lc->id] = array("name" => $lc->name, "parent_id" => $lc->parentId, "level" => $lc->level, "child_list" => array());
            }
            $sub_category_array[$sc->id] = array("name" => $sc->name, "parent_id" => $sc->parentId, "level" => $sc->level, "child_list" => $last_category_array);
        }
        $category_data_array[$c->id] = array("name" => $c->name, "parent_id" => $c->parentId, "level" => $c->level, "child_list" => $sub_category_array);
    }
    return $category_data_array;
}

function getYoutubeData($video_id)
{
    $video_data = Youtube::getVideoInfo($video_id);
    $youtube_data = array();
    if ($video_data) {
        $video_duration = "";
        if ($video_data->contentDetails) {
            $converted_date = new CarbonInterval($video_data->contentDetails->duration);
            $video_duration = formatDuration($converted_date->h, $converted_date->i, $converted_date->s);
        }
        if ($video_data->snippet) {
            $youtube_data["title"] = $video_data->snippet->title;
            $youtube_data["description"] = nl2br($video_data->snippet->description);
            $youtube_data["publish_date"] = Carbon::parse($video_data->snippet->publishedAt)->format("Y-m-d H:i:s");
            $youtube_data["tags"] = (isset($video_data->snippet->tags)) ? implode(",", $video_data->snippet->tags) : "";
            $youtube_data["thumbnail_image"] = "";
            if (isset($video_data->snippet->thumbnails->maxres)) {
                $youtube_data["thumbnail_image"] = $video_data->snippet->thumbnails->maxres->url;
            } else if (isset($video_data->snippet->thumbnails->high)) {
                $youtube_data["thumbnail_image"] = $video_data->snippet->thumbnails->high->url;
            }
        }
        $youtube_data["duration"] = $video_duration;
    }
    return $youtube_data;
}

function formatDuration($h, $i, $s)
{
    $duration = array();
    if ($h > 0) {
        array_push($duration, ($h < 10 ? ("0" . $h) : $h));
    }
    if ($i > 0) {
        array_push($duration, (($i < 10 && $h > 0) ? ("0" . $i) : $i));
    } else {
        array_push($duration, ($h > 0 ? "00" : "0"));
    }
    array_push($duration, ($s < 10 ? ("0" . $s) : $s));
    return (implode(":", $duration));
}

/**
 *  Method to send notification
 *
 * @return \Illuminate\Http\JsonResponse
 */
function sendNotification($heading, $id, $title, $type, $category_id)
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
        foreach (count((array)$user_data) > 0 ? $user_data : array() as $u) {
            $category_ids = getUserCategorySetting($u->id);
            if (!in_array($category_id, $category_ids)) {
                continue;
            }
            $alert = Alert::create();
            $alert = $alert->setBody($heading);
            $payload = Payload::create()->setAlert($alert);

            //add custom value to your notification, needs to be customized
            $payload->setCustomValue('id', $id);
            $payload->setCustomValue('title', $title);
            $payload->setCustomValue('type', $type);
            if ($u->soundStatus) {
                //set notification sound to default
                $payload->setSound('default');
            }
            $notifications[] = new Notification($payload, $u->deviceToken);
        }
        if (count($notifications) > 0) {
            $client = new Client($authProvider, false);
            $client->addNotifications($notifications);
            $client->push();
        }
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

/**
 *  Method to send notification
 *
 * @return \Illuminate\Http\JsonResponse
 */
function sendNotificationAndroid($heading, $id, $title, $type, $category_id, $summary = "")
{
    try {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $user_data = AppUserDetail::where("type", 2)->whereRaw("deviceToken IS NOT NULL")->get();
        $deviceTokens = array();
        foreach (count((array)$user_data) > 0 ? $user_data : array() as $u) {
            $category_ids = getUserCategorySetting($u->id);
            if (!in_array($category_id, $category_ids)) {
                continue;
            }
            array_push($deviceTokens, $u->deviceToken);
        }
        if (count($deviceTokens) > 0) {
            $notification = [
                'heading' => $heading,
            ];

            $extraNotificationData = ["message" => $notification, "moredata" => array("id" => $id, "title" => $title, "type" => $type, "summary" => $summary)];

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
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
            curl_exec($ch);
        }
    } catch (\Exception $e) {
        return $this->sendResultJSON("0", $e->getMessage());
    }
}

function getUserCategorySetting($user_id)
{
    $category_id_array = array();
    $category_data = UserCategoryNotificationDetail::selectRaw("GROUP_CONCAT(categoryId) as cat_ids")->where("userId", $user_id)->where("isNotificationOn", 1)->first();
    if ($category_data && $category_data->cat_ids != "") {
        $category_id_array = explode(",", $category_data->cat_ids);
    }
    return $category_id_array;
}

/**
 * Method to get advertisement
 */
function getAdvertisementData($image_type, $category_id = "")
{
    $data = AdvertisementDetail::selectRaw("advertisement_details.id,advertisement_details.link,advertisement_file_details.imageName,advertisement_file_details.categoryId")->leftJoin("advertisement_file_details", function ($join) {
        $join->on('advertisement_details.id', '=', 'advertisement_file_details.advertisementId');
    })->where("advertisement_details.status", 1)->where("advertisement_file_details.advertisementFor", "web");
    if ($category_id != "") {
        $data = $data->whereRaw("(FIND_IN_SET(?,advertisement_file_details.categoryId)  > 0)", [$category_id]);
    }
    if ($image_type == "sidebar") {
        $data = $data->where("advertisement_file_details.position", "sidebar");
    } else if ($image_type == "sidebar_responsive") {
        $data = $data->where("advertisement_file_details.position", "sidebar_responsive");
    } else if ($image_type == "middle") {
        $data = $data->where("advertisement_file_details.position", "middle");
    } else if ($image_type == "bottom") {
        $data = $data->where("advertisement_file_details.position", "bottom");
    }
    $data = $data->get();
    return $data;
}

function getArticleDatahome($category_ids, $limit, $isfeature, $isslider, $istrending)
{

    $data = ArticleDetail::selectRaw("article_details.id,article_details.heading,article_details.categoryId,article_details.isVideo,article_details.publishDate,article_details.updated_at,article_details.featureId,article_details.categoryId,article_details.viewCount,article_details.created_at,article_file_details.articleId,article_file_details.fileName,article_file_details.duration,article_file_details.thumbnailImage,article_file_details.isMain")->leftJoin("article_file_details", function ($join) {
        $join->on('article_details.id', '=', 'article_file_details.articleId');
    })->where('article_details.status', 1)->where('isVideo', 0);
    if (count($category_ids) > 0) {
        $data = $data->whereIn('categoryId', $category_ids);
    }

    if ($istrending == "true") {
        $data = $data->orderByRaw("article_details.created_at >= '" . Carbon::now()->subWeeks(2)->format("Y-m-d H:i:s") . "' DESC")->orderBy('viewCount', 'desc');
    } else {

        if ($isfeature == "false") {
            if ($isslider == "true") {
                $get_slider_id = get_feature_id("Slider");
                $data = $data->whereRaw('FIND_IN_SET(' . $get_slider_id . ',featureId) > 0');
            } else {
                $get_slider_id = get_feature_id("Slider");
                $data = $data->whereRaw('FIND_IN_SET(' . $get_slider_id . ',featureId) = 0');
            }
        } else {

            $get_feature_id = get_feature_id("Featured");
            $data = $data->whereRaw('FIND_IN_SET(' . $get_feature_id . ',featureId) > 0');

        }

        $data = $data->orderBy('publishDate', 'desc');
    }
    $data = $data->where('isMain', 1)->take($limit)->get();
    return $data;
}

function getArticlelist($category_ids, $limit, $article_ids = array(),$offset = 0)
{

    $data = ArticleDetail::selectRaw("article_details.id,article_details.heading,article_details.categoryId,article_details.isVideo,article_details.publishDate,article_details.updated_at,article_details.created_at,article_file_details.articleId,article_file_details.fileName,article_file_details.duration,article_file_details.thumbnailImage,article_file_details.isMain")->leftJoin("article_file_details", function ($join) {
        $join->on('article_details.id', '=', 'article_file_details.articleId');
    })->where('article_details.status', 1)->where('isVideo', 0)->orderBy('publishDate', 'desc');
    if (count($category_ids) > 0) {
        $data = $data->whereIn('categoryId', $category_ids);
    }

    if (count($article_ids) > 0) {
        $data = $data->whereNotIn('article_details.id', $article_ids);
    }
    
    if ($offset){

        $data->skip($offset);

    }

    if ($limit == null) {
        $data = $data->where('isMain', 1)->paginate(15);
    } else {
        $data = $data->where('isMain', 1)->take($limit)->get();
    }

//    dd($data->toSql());

    return $data;
}

function getAuthorImage($image_name)
{
    return (($image_name == "" || !file_exists(public_path("uploads/team/" . $image_name))) ? url("uploads/team/avatar.png") : url("uploads/team/" . $image_name));
}


function getBusinessImage($image_name)
{
    return (($image_name == "" || !file_exists(public_path("uploads/business/logo/" . $image_name))) ? asset("images/image-not-found.png") : url("uploads/business/logo/" . $image_name));
}

function getUnapprovedBusinessImage($image_name)
{
    return (($image_name == "" || !file_exists(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/logo/" . $image_name)) ? asset("images/image-not-found.png") : url(config("constants.admin_sub_folder_name") . "/public/uploads/business/logo/" . $image_name)));
}

function getEventBanner($image_name)
{
    return (($image_name == "" || !file_exists(public_path("uploads/event/" . $image_name))) ? asset("images/image-not-found.png") : url("uploads/event/" . $image_name));
}

function getEventData($e)
{
    $price = json_decode($e->price);
    return array("id" => $e->id, "name" => $e->name, "description" => $e->description, "event_date" => Carbon::parse($e->eventDate)->format("M d, Y") . " " . Carbon::parse($e->eventTime)->format("h:i a"), "event_time" => ($e->eventTime != null ? Carbon::parse($e->eventTime)->format("h:i A") : ""), "venue" => $e->venue, "venueAddress" => $e->venueAddress, "venuePhone" => $e->venuePhone, "organizer_name" => $e->organizerName, "organizer_phone" => $e->organizerPhone, "organizer_email" => $e->organizerEmail, "organizer_website" => $e->organizerWebsite, "banner_image" => getEventBanner($e->bannerImage), "booking_link" => $e->bookingLink, "price" => config("constants.event_price_option")[$price->option], "cost" => $price->value, "link_text" => $e->linkText, "category_name" => $e->eventCategory->name);
}

function getRelatedArticles($business_id, $name)
{
    $business_categories = BusinessCategoryDetail::where("businessId", $business_id)->get();
    $articles = ArticleDetail::where("status", 1)->where("isVideo", 0);
    $category = array();
    foreach (count((array)$business_categories) > 0 ? $business_categories : array() as $bc) {
        if ($bc->categoryDetail) {
            $cat_name = $bc->categoryDetail->name;
            array_push($category, $cat_name);
        }
    }
    $search_terms = array_merge($category, [$name]);
    $articles = $articles->where(function ($query) use ($search_terms) {
        foreach ($search_terms as $term) {
            $query->orWhereRaw("FIND_IN_SET(?,REPLACE(metaTag,';',',')) > 0", [$term])->orWhere("description", "like", "%" . $term . "%");
        }
    });
    $articles = $articles->orderBy("publishDate", "desc")->take(10)->get();
    return array("article" => $articles, "category" => $category);
}

function getBusinessAddress($b)
{
    $address_array = array();
    $address_fields = array("address1", "address2", "city", "province", "country", "postalCode");
    foreach ($address_fields as $field) {
        if ($b->{$field} != "") {
            array_push($address_array, $b->{$field});
        }
    }
    return implode(",", $address_array);
}

function getUserAvatar($avatar)
{
    $file = '';
    if ($avatar != '' && file_exists(public_path() . '/uploads/avatar/' . $avatar)) {
        $file = asset('uploads') . '/avatar/' . $avatar;
    } else {
        $file = url('images/add_profile.png');
    }
    return $file;
}

function getUserAvatarForLeaderboard($avatar)
{
    $file = '';
    if ($avatar != '' && file_exists(public_path() . '/uploads/avatar/' . $avatar)) {
        $file = asset('uploads') . '/avatar/' . $avatar;
    } else {
        $file = url('images/icon_profile.png');
    }
    return $file;
}

function getNotificationList()
{
    $notifications = NotificationDetail::orderBy("sentTime", "desc")->get();
    $result = array();
    foreach (!$notifications->isEmpty() ? $notifications : array() as $n) {
        $business_detail = ($n->userWiseBusinessDetail ? $n->userWiseBusinessDetail : $n->businessDetail);
        $redirect_url = ($n->userWiseBusinessDetail ? url("edit-business/" . $n->userBusinessId) : url("edit-approved-business/" . $n->businessId));
        $notificationText = str_replace("{user_name}", ($n->userDetail ? $n->userDetail->name : ""), $n->notificationText);
        array_push($result, array("id" => $n->id, "notificationText" => $notificationText, "sentTime" => Carbon::parse($n->sentTime)->diffForHumans(), "isRead" => $n->isRead, "logo" => ($n->userWiseBusinessDetail ? getUnapprovedBusinessImage($business_detail->logo) : getBusinessImage($business_detail->logo)), "redirect_url" => $redirect_url));
    }
    return $result;
}

function getNotificationCount()
{
    return NotificationDetail::where("isRead", 0)->count();
}
