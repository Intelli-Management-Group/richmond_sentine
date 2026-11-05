<?php

namespace App\Http\Controllers\Api;

use App\Models\AdvertisementDetail;
use App\Models\AdvertisementFileDetail;
use App\Models\ArticleDetail;
use App\Models\ArticleFeatureDetail;
use App\Models\AuthorDetail;
use App\Models\CategoryDetail;
use App\Models\ContactUsDetail;
use App\Models\CouponDetail;
use App\Models\FooterContentDetail;
use App\Models\OurTeamDetail;
use App\Models\User;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use VideoThumbnail;
use Carbon\Carbon;
use App\Models\ArticleFileDetail;

class CronController extends Controller
{
    private $another_db_connection = "";

    public function __construct()
    {
        $this->another_db_connection = DB::connection("mysql_external");
    }
    /**
     *  Method to get sync author data
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function getAuthorData()
    {
        try {
            $old_author_data = $this->another_db_connection->table("author_info")->get();
            $author_id_array = array();
            foreach (count($old_author_data) > 0 ? $old_author_data : array() as $o) {
                $author = new AuthorDetail();
                $author->id = $o->id;
                $author->name = $o->author_name;
                $author->email = $o->author_email;
                $author->twitterHandle = $o->author_twitter;
                $author->facebookHandle = "";
                $author->profileImage = $o->authorimage;
                $author->status = intval($o->status);
                if ($o->authorimage != "" && file_exists(public_path("uploads/backup/team/" . $o->authorimage))) {
                    File::move(public_path("uploads/backup/team/" . $o->authorimage), public_path("uploads/team/" . $o->authorimage));
                }
                $author->save();

                array_push($author_id_array, $o->id);
            }
            return $this->sendResultJSON("1", "Author updated successfully", array("author_id_array" => $author_id_array));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
    /**
     *  Method to get sync author data
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function getArticleData()
    {
        try {
            $start = request("start") ? request("start") : 0;
            $limit = request("limit") ? request("limit") : 100;
            //$author_list = array("Martin van den Hemel", "Don Fennell", "Lorraine Graves");
            // $get_author_data = AuthorDetail::selectRaw("GROUP_CONCAT(id) as author_ids")->whereIn("name", $author_list)->first();
            // $author_ids_take = array();
            // if ($get_author_data) {
            //     $author_ids_take = explode(",", $get_author_data->author_ids);
            // }
            $old_article_data = $this->another_db_connection->table("article_master")->skip($start)->take($limit)->orderBy("article_id", "desc")->get();
            $predefined_meta_data = $this->another_db_connection->table("tbl_metatag")->selectRaw("meta_id,meta_name")->get();
            $predefined_meta_data_array = array();
            foreach (count($predefined_meta_data) > 0 ? $predefined_meta_data : array() as $p) {
                $predefined_meta_data_array[$p->meta_id] = $p->meta_name;
            }
            $feature_id = ArticleFeatureDetail::select("id")->where("name", "Featured")->first();
            $article_id_array = array();
            foreach (count($old_article_data) > 0 ? $old_article_data : array() as $a) {
                $meta_tag_array = array();
                if ($a->predefined_meta != "") {
                    $article_predefined_meta_data = explode(",", $a->predefined_meta);
                    foreach ($article_predefined_meta_data as $m) {
                        array_push($meta_tag_array, $predefined_meta_data_array[$m]);
                    }
                }
                if ($a->metakey != "") {
                    $existing_meta_tag = explode(",", $a->metakey);
                    foreach ($existing_meta_tag as $e) {
                        array_push($meta_tag_array, $e);
                    }
                }
                $subcategory_map = array("2" => "Business", "3" => "Latest News", "4" => "Provincial News", "5" => "National News", "6" => "International", "7" => "Sports", "8" => "Sports", "12" => "Crime", "13" => "Crime", "14" => "Crime");
                $category_map = array("15" => "Provincial News", "17" => "Arts & Culture", "18" => "Business", "19" => "Sports", "20" => "Crime", "21" => "Social Media");
                $article = new ArticleDetail();
                $article->heading = $a->heading;
                $article->description = $a->description;
                $article->summary = $a->my100word;
                $feature_id_array = array();
                array_push($feature_id_array, $feature_id->id);
                if ($a->slider == "on") {
                    $slider_id = ArticleFeatureDetail::select("id")->where("name", "Slider")->first();
                    if ($slider_id) {
                        array_push($feature_id_array, $slider_id->id);
                    }
                }
                $article->featureId = implode(",", $feature_id_array);
                $article->authorId = $a->author_id;
                if ($a->sub_category != "") {
                    if (isset($subcategory_map[$a->sub_category])) {
                        $get_category_id = CategoryDetail::select("id")->where("name", $subcategory_map[$a->sub_category])->where("isDisplayInFrontend", 1)->first();
                        if ($get_category_id) {
                            $article->categoryId = $get_category_id->id;
                        }
                    }
                } else {
                    if (isset($category_map[$a->cat_id])) {
                        $get_category_id = CategoryDetail::select("id")->where("name", $category_map[$a->cat_id])->first();
                        if ($get_category_id) {
                            $article->categoryId = $get_category_id->id;
                        }
                    }
                }
                $article->publishDate = $a->publish_date;
                $article->expiryDate = null;
                //$article->imageCaption = $a->caption;
                //$article->imageCredit = $a->credits;

                //$article->neighberhood = $a->neighbourhood;
                $article->metaTag = implode(",", $meta_tag_array);
                $article->status = $a->status;

                $article->source = $a->source;
                $article->created_at = $a->created_date;
                $article->updated_at = $a->update_date;
                $article->save();

                if ($a->horizontal_image != "") {
                    $article_file = new ArticleFileDetail();
                    $article_file->articleId = $article->id;
                    $article_file->type = "image";
                    $article_file->fileName = $a->horizontal_image;
                    $article_file->isMain = 1;
                    $article_file->save();
                    if ($a->horizontal_image != "" && file_exists(public_path("uploads/backup/article/" . $a->horizontal_image))) {
                        File::move(public_path("uploads/backup/article/" . $a->horizontal_image), public_path("uploads/article/" . $a->horizontal_image));
                    }
                }

                if ($a->video_file != "") {
                    $article_file = new ArticleFileDetail();
                    $article_file->articleId = $article->id;
                    $article_file->type = "video";
                    $article_file->fileName = $a->video_file;
                    $article_file->isMain = ($a->horizontal_image == "" ? 1 : 0);
                    $article_file->save();
                    if ($a->video_file != "" && file_exists(public_path("uploads/backup/article/" . $a->video_file))) {
                        File::move(public_path("uploads/backup/article/" . $a->video_file), public_path("uploads/article/" . $a->video_file));

                        $ffmpeg = FFProbe::create([
                            'ffmpeg.binaries' => config('video-thumbnail.binaries.ffmpeg'),
                            'ffprobe.binaries' => config('video-thumbnail.binaries.ffprobe'),
                        ]);
                        $video_file_name = (public_path('uploads/article') . "/" . $a->video_file);
                        if (file_exists($video_file_name)) {
                            $video_obj = $ffmpeg->format($video_file_name);
                            $seconds = $video_obj->get("duration");
                            $h = floor($seconds / 3600);
                            $i = ($seconds / 60) % 60;
                            $s = $seconds % 60;
                            $article_file->duration = formatDuration($h, $i, $s);
                        }
                        $file_name = ($article->id . "_" . $article_file->id . '.jpg');
                        VideoThumbnail::createThumbnail($video_file_name, public_path('uploads/video_thumbnail/'), $file_name, 2, 750, 375);
                        $article_file->thumbnailImage = $file_name;
                        $article_file->save();
                    }
                }
                array_push($article_id_array, $a->article_id);
            }
            return $this->sendResultJSON("1", "Article updated successfully", array("article_id_array" => $article_id_array));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
    public function updateCPArticleData()
    {
        try {
            $start = request("start") ? request("start") : 0;
            $limit = request("limit") ? request("limit") : 100;
            $author_list = array("The Canadian Press");
            $get_author_data = AuthorDetail::select("id")->whereIn("name", $author_list)->first();
            $author_ids_take = array();
            if ($get_author_data) {
                $author_ids_take = $get_author_data->id;
            }
            $old_article_data = $this->another_db_connection->table("article_master")->where("author_id", $author_ids_take)->where("created_date", ">=", Carbon::now()->subMonths(2)->format("Y-m-01 H:i:s"))->skip($start)->take($limit)->orderBy("article_id", "desc")->get();
            $predefined_meta_data = $this->another_db_connection->table("tbl_metatag")->selectRaw("meta_id,meta_name")->get();
            $predefined_meta_data_array = array();
            foreach (count($predefined_meta_data) > 0 ? $predefined_meta_data : array() as $p) {
                $predefined_meta_data_array[$p->meta_id] = $p->meta_name;
            }
            $feature_id = ArticleFeatureDetail::select("id")->where("name", "Featured")->first();
            $article_id_array = array();
            foreach (count($old_article_data) > 0 ? $old_article_data : array() as $a) {
                $meta_tag_array = array();
                if ($a->predefined_meta != "") {
                    $article_predefined_meta_data = explode(",", $a->predefined_meta);
                    foreach ($article_predefined_meta_data as $m) {
                        array_push($meta_tag_array, $predefined_meta_data_array[$m]);
                    }
                }
                if ($a->metakey != "") {
                    $existing_meta_tag = explode(",", $a->metakey);
                    foreach ($existing_meta_tag as $e) {
                        array_push($meta_tag_array, $e);
                    }
                }
                $subcategory_map = array("2" => "Business", "3" => "Latest News", "4" => "Provincial News", "5" => "National News", "6" => "International", "7" => "Sports", "8" => "Sports", "12" => "Crime", "13" => "Crime", "14" => "Crime");
                $category_map = array("15" => "Provincial News", "17" => "Arts & Culture", "18" => "Business", "19" => "Sports", "20" => "Crime", "21" => "Social Media");
                $article = new ArticleDetail();
                $article->heading = $a->heading;
                $article->description = $a->description;
                $article->summary = $a->my100word;
                $feature_id_array = array();
                array_push($feature_id_array, $feature_id->id);
                if ($a->slider == "on") {
                    $slider_id = ArticleFeatureDetail::select("id")->where("name", "Slider")->first();
                    if ($slider_id) {
                        array_push($feature_id_array, $slider_id->id);
                    }
                }
                $article->featureId = implode(",", $feature_id_array);
                $article->authorId = $a->author_id;
                if ($a->sub_category != "") {
                    if (isset($subcategory_map[$a->sub_category])) {
                        $get_category_id = CategoryDetail::select("id")->where("name", $subcategory_map[$a->sub_category])->where("isDisplayInFrontend", 1)->first();
                        if ($get_category_id) {
                            $article->categoryId = $get_category_id->id;
                        }
                    }
                } else {
                    if (isset($category_map[$a->cat_id])) {
                        $get_category_id = CategoryDetail::select("id")->where("name", $category_map[$a->cat_id])->first();
                        if ($get_category_id) {
                            $article->categoryId = $get_category_id->id;
                        }
                    }
                }
                $article->publishDate = $a->publish_date;
                $article->expiryDate = null;
                //$article->imageCaption = $a->caption;
                //$article->imageCredit = $a->credits;

                //$article->neighberhood = $a->neighbourhood;
                $article->metaTag = implode(",", $meta_tag_array);
                $article->status = $a->status;

                $article->source = $a->source;
                $article->created_at = $a->created_date;
                $article->updated_at = $a->update_date;
                $article->save();

                if ($a->horizontal_image != "") {
                    $article_file = new ArticleFileDetail();
                    $article_file->articleId = $article->id;
                    $article_file->type = "image";
                    $article_file->fileName = $a->horizontal_image;
                    $article_file->isMain = 1;
                    $article_file->save();
                    if ($a->horizontal_image != "" && file_exists(public_path("uploads/backup/article/" . $a->horizontal_image))) {
                        File::move(public_path("uploads/backup/article/" . $a->horizontal_image), public_path("uploads/article/" . $a->horizontal_image));
                    }
                }

                if ($a->video_file != "") {
                    $article_file = new ArticleFileDetail();
                    $article_file->articleId = $article->id;
                    $article_file->type = "video";
                    $article_file->fileName = $a->video_file;
                    $article_file->isMain = ($a->horizontal_image == "" ? 1 : 0);
                    $article_file->save();
                    if ($a->video_file != "" && file_exists(public_path("uploads/backup/article/" . $a->video_file))) {
                        File::move(public_path("uploads/backup/article/" . $a->video_file), public_path("uploads/article/" . $a->video_file));

                        $ffmpeg = FFProbe::create([
                            'ffmpeg.binaries' => config('video-thumbnail.binaries.ffmpeg'),
                            'ffprobe.binaries' => config('video-thumbnail.binaries.ffprobe'),
                        ]);
                        $video_file_name = (public_path('uploads/article') . "/" . $a->video_file);
                        if (file_exists($video_file_name)) {
                            $video_obj = $ffmpeg->format($video_file_name);
                            $seconds = $video_obj->get("duration");
                            $h = floor($seconds / 3600);
                            $i = ($seconds / 60) % 60;
                            $s = $seconds % 60;
                            $article_file->duration = formatDuration($h, $i, $s);
                        }
                        $file_name = ($article->id . "_" . $article_file->id . '.jpg');
                        VideoThumbnail::createThumbnail($video_file_name, public_path('uploads/video_thumbnail/'), $file_name, 2, 750, 375);
                        $article_file->thumbnailImage = $file_name;
                        $article_file->save();
                    }
                }
                array_push($article_id_array, $a->article_id);
            }
            return $this->sendResultJSON("1", "Article updated successfully", array("article_id_array" => $article_id_array));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
    /**
     *  Method to get about us data
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function updateFooterContent()
    {
        try {
            $about_us_data = $this->another_db_connection->table("about_us")->first();
            if ($about_us_data) {
                $insert_data = new FooterContentDetail();
                $insert_data->type = "about_us";
                $insert_data->content = json_encode(array(
                    "description" => $about_us_data->description,
                    "profile_image" => $about_us_data->profile_image,
                    "history" => $about_us_data->history,
                ));
                $insert_data->save();
            }

            $team_data = $this->another_db_connection->table("team_info")->get();
            foreach (count($team_data) > 0 ? $team_data : array() as $t) {
                $data = new OurTeamDetail();
                $data->name = $t->name;
                $data->profile_image = $t->profile_image;
                $data->position = $t->title;
                $data->twitter_handle = $t->twitter;
                $data->email_id = $t->email;
                $data->description = $t->description;
                $data->save();
            }

            $contact_us_data = $this->another_db_connection->table("contact_us")->first();
            if ($contact_us_data) {
                $c_data = new ContactUsDetail();
                $c_data->email_id = $contact_us_data->email;
                $c_data->phone = $contact_us_data->phone;
                $c_data->mailing_address = $contact_us_data->Mailing_Address;
                $c_data->save();
            }
            return $this->sendResultJSON("1", "Footer content updated");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update coupon data
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function updateCouponData()
    {
        try {
            $old_coupon_data = $this->another_db_connection->table("Coupon_Master")->get();
            $get_coupan_cat = $this->another_db_connection->table("coupon_cat")->get();
            $category_array = array();

            foreach (count($get_coupan_cat) > 0 ? $get_coupan_cat : array() as $gc) {
                $category_array[$gc->ID] = $gc->cat_name;
            }

            $coupan_category_array = array();
            $coupan_category = CategoryDetail::selectRaw("id,name")->where("isCouponCategory", 1)->get();
            foreach (count($coupan_category) > 0 ? $coupan_category : array() as $cc) {
                $coupan_category_array[$cc->name] = $cc->id;
            }
            $coupon_id_array = array();
            foreach (count($old_coupon_data) > 0 ? $old_coupon_data : array() as $c) {
                $coupon = new CouponDetail();
                $coupon->name = $c->Coupon_Name;
                $coupon->heading = $c->Coupon_Heading;
                $coupon->categoryId = $coupan_category_array[$category_array[$c->Coupon_Cat_Id]];
                $coupon->highlights = $c->Coupon_Highlights;
                $coupon->finePrints = $c->Coupon_FinePrint;
                $coupon->detail = $c->Coupon_Detail;
                $coupon->companyName = $c->Company_Name;
                $coupon->companyPhone = $c->Company_Phone;
                $coupon->companyEmail = $c->Company_Email;
                $coupon->companyAddress = $c->Company_Address;
                $coupon->bannerImage = $c->Coupon_Image;
                $coupon->thumbnailImage = $c->Coupon_Thumbnail;
                $coupon->originalPrice = $c->Original_Price;
                $coupon->discountPrice = $c->Discount_Price;
                $coupon->publishDate = $c->Coupon_PublishDate;

                $columns_to_take = array("mon_chk" => "mon", "tue_chk" => "tue", "wed_chk" => "wed", "thr_chk" => "thr", "fri_chk" => "fri", "sat_chk" => "sat", "sun_chk" => "sun");
                $daywisedata = array();
                foreach ($columns_to_take as $key => $value) {
                    if (intval($c->{$key}) == 0) {
                        $daywisedata[$value] = $c->{$value};
                    } else {
                        $daywisedata[$value] = "";
                    }
                }

                $coupon->daywiseTime = json_encode($daywisedata);
                $coupon->status = $c->Coupon_Status;
                $coupon->save();
                array_push($coupon_id_array, $c->id);
            }
            return $this->sendResultJSON("1", "Coupon updated successfully", array("coupon_id_array" => $coupon_id_array));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update coupon data
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function updateVideoData()
    {
        try {
            $old_video_data = $this->another_db_connection->table("gallery")->where("file_type", "video")->get();
            $video_id_array = array();
            foreach (count($old_video_data) > 0 ? $old_video_data : array() as $v) {
                $video = new ArticleDetail();
                $video->isVideo = 1;
                $video->videoFile = $v->file_name;
                $video->heading = $v->heading;

                $video_credit = $v->credit;
                $video_credit_data = explode(",", $video_credit);
                // $author_list = array("Martin van den Hemel", "Don Fennell", "Lorraine Graves");
                $is_author_found = 0;
                foreach (count($video_credit_data) > 0 ? $video_credit_data : array() as $vc) {
                    if (preg_match("/Video by/i", $vc)) {
                        $remaining_data = explode("Video by", $vc);
                        if (count($remaining_data) == 2) {
                            $author_name = trim($remaining_data[1]);
                            //if (in_array($author_name, $author_list)) {
                            $author_data = AuthorDetail::select("id")->where("name", $author_name)->first();
                            if ($author_data) {
                                $video->authorId = $author_data->id;
                                $is_author_found = 1;
                            }
                            //}
                        }
                    } else {
                        $author_name = trim($vc);
                        //if (in_array($author_name, $author_list)) {
                        $author_data = AuthorDetail::select("id")->where("name", $author_name)->first();
                        if ($author_data) {
                            $video->authorId = $author_data->id;
                            $is_author_found = 1;
                        }
                        //}
                    }
                }
                if (!$is_author_found) {
                    continue;
                }
                $ffmpeg = FFProbe::create([
                    'ffmpeg.binaries' => config('video-thumbnail.binaries.ffmpeg'),
                    'ffprobe.binaries' => config('video-thumbnail.binaries.ffprobe'),
                ]);
                if ($v->file_name != "" && file_exists(public_path("uploads/backup/video/" . $v->file_name))) {
                    File::move(public_path("uploads/backup/video/" . $v->file_name), public_path("uploads/video/" . $v->file_name));
                }
                $video_file_name = (public_path('uploads/video') . "/" . $v->file_name);
                if ($v->file_name != "" && file_exists($video_file_name)) {
                    $video_obj = $ffmpeg->format($video_file_name);
                    $seconds = $video_obj->get("duration");
                    $h = floor($seconds / 3600);
                    $i = ($seconds / 60) % 60;
                    $s = $seconds % 60;
                    $video->videoDuration = formatDuration($h, $i, $s);
                }
                $video->videoCredit = $v->credit;
                $video->publishDate = $v->publish_date;
                $video->created_at = $v->publish_date;

                $video->status = $v->status;
                $video->save();

                $file_name = ($video->id . '.jpg');
                VideoThumbnail::createThumbnail((public_path('uploads/video') . "/" . $v->file_name), public_path('uploads/video_thumbnail/'), $file_name, 2, 750, 375);
                $video->videoThumbnail = $file_name;
                $video->updated_at = $v->publish_date;
                $video->save();

                array_push($video_id_array, $v->id);
            }
            return $this->sendResultJSON("1", "Video updated successfully", array("video_id_array" => $video_id_array));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
    /**
     *  Method to update admin user data
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function updateAdminLoginData()
    {
        try {
            $old_admin_data = $this->another_db_connection->table("admin_login")->get();
            foreach (count($old_admin_data) > 0 ? $old_admin_data : array() as $o) {
                $user = new User();
                $user->user_name = $o->user_name;
                $user->password = $o->password;
                $user->created_at = $o->created_date;
                $user->updated_at = $o->created_date;
                $user->save();
            }
            return $this->sendResultJSON("1", "Admin data updated successfully");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
    /**
     *  Method to update advertisement data
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function updateAdvertisementData()
    {
        try {
            $old_advertisement_data = $this->another_db_connection->table("advertisement_master")->get();

            $advertisement_ids_array = array();
            foreach (count($old_advertisement_data) > 0 ? $old_advertisement_data : array() as $o) {
                $ad_data = new AdvertisementDetail();
                $ad_data->placement = $o->ads_placement;
                $ad_data->aboveCaption = $o->above_caption;
                $ad_data->belowCaption = $o->below_caption;
                $ad_data->link = $o->ad_link;
                $ad_data->type = 1;

                $ad_data->status = $o->status;
                $ad_data->publishDate = $o->published_date;
                $ad_data->createdBy = $o->user_id;
                $ad_data->updatedBy = $o->user_id;
                $ad_data->created_at = $o->created_date;
                $ad_data->updated_at = $o->created_date;
                $ad_data->save();

                $categoryIds = explode(",", $o->category);
                foreach (count($categoryIds) > 0 ? $categoryIds : array() as $c) {
                    $ad_file_detail = new AdvertisementFileDetail();
                    $ad_file_detail->advertisementId = $ad_data->id;
                    $ad_file_detail->categoryId = $c;
                    $fileName = "";
                    $file_data = $this->another_db_connection->table("advertisement_file")->where("ads_id", $o->ads_id)->first();
                    if ($file_data) {
                        $fileName = $file_data->advert_file;
                    }
                    if ($o->ads_placement == "articles") {
                        $ad_file_detail->horizontalImage = $fileName;
                    } else if ($o->ads_placement == "bottom") {
                        $ad_file_detail->horizontalImage = $fileName;
                    } else if ($o->ads_placement == "sidebar") {
                        $ad_file_detail->squareImage = $fileName;
                    }
                    $ad_file_detail->save();
                }
                array_push($advertisement_ids_array, $o->ads_id);
            }
            return $this->sendResultJSON("1", "Advertisement data updated successfully", array("advertisement_data" => $advertisement_ids_array));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update advertisement data
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function updateArticleCaptionCreditData()
    {
        try {
            $article_id = array();
            $article_file_details = ArticleFileDetail::where("isMain", 1)->get();
            foreach (count($article_file_details) > 0 ? $article_file_details : array() as $f) {
                if (isset($f->articleDetail)) {
                    $articleDetail = $f->articleDetail;
                    $f->caption = $articleDetail->imageCaption;
                    $f->credit = $articleDetail->imageCredit;
                    $f->save();
                    array_push($article_id,$f->articleId);
                }
            }
            return $this->sendResultJSON("1", "Article data updated successfully", array("article_ids" => $article_id));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
}
