<?php

namespace App\Http\Controllers;

use App\Models\ArticleDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ArticleFeatureDetail;
use App\Models\AuthorDetail;
use App\Models\CategoryDetail;
use Validator;
use Illuminate\Support\Facades\File;
use App\Models\ArticleFileDetail;

class ArticleController extends Controller
{
    /**
     *  Method to load published article list
     *
     * @return \Illuminate\Http\JsonResponse
     */



    public function loadArticleList(Request $request)
    {
        try {
            return view("article.list");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load published article list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function publishedArticleListPaginationData(Request $request)
    {       
        try {
            
            $start = \Request::input('start');
            $end = \Request::input('length');
            $search_value = \Request::input('search')['value'];
            $search_value = strtolower($search_value);

            $order_detail = \Request::input('order');
            
            $sort_order = "DESC";
            $sort_column_name = "publishDate";
            $sort_columns = array("1" => "article_details.heading", "2" => "category_details.name", "4" => "article_details.publishDate", "5" => "article_details.viewCount");
            if (count((array)$order_detail) > 0) {
                $sort_column_name = $sort_columns[$order_detail[0]['column']];
                $sort_order = strtoupper($order_detail[0]['dir']);
            }
            $article_data = ArticleDetail::selectRaw("article_details.id,article_details.heading,article_details.categoryId,article_details.publishDate,article_details.status,article_details.isVideo,article_details.viewCount")->leftJoin("category_details", function ($join) {
                $join->on('article_details.categoryId', '=', 'category_details.id');
            })->where("article_details.status", 1);

            if ($sort_column_name != "category") {
                $article_data = $article_data->orderBy($sort_column_name, $sort_order);
            }
            if ($search_value != "") {
                $article_data = $article_data->whereRaw("(article_details.heading like '%" . $search_value . "%' OR category_details.name like '%" . $search_value . "%')");
            }
            $total_articles = $article_data->count();
            $article_data = $article_data->skip($start)->take($end)->get();

            $article_data_array = array();
            foreach (count((array)$article_data) > 0 ? $article_data : array() as $a) {
                $publish_button = "";
                $onclick = "window.location='" . url("unpublish-article" . "/" . $a->id) . "'";
                $publish_button = '<span style="cursor:default"><input type="submit" onclick="' . $onclick . '" class="publish_button" value="Unpublish"/></span>';

                $main_image_data = $a->articleFileDetail->where("isMain", 1)->first();
                $image = "";
                if (!$a->isVideo) {
                    if ($main_image_data->type == "image") {
                        $image = ("uploads/article/" . $main_image_data->fileName);
                        if (!file_exists(public_path($image))) {
                            $image = ("images/image-not-found.png");
                        }
                    } else {
                        $image = ("images/Video.png");
                    }
                } else {
                    $image = ("images/Video.png");
                }
                $delete_onclick = 'javascript:deleteArticle(' . $a->id . ');';
                array_push($article_data_array, array(
                    "checkbox" => "<input type='checkbox' value='" . $a->id . "' class='delete_entity'/>",
                    "heading" => $a->heading,
                    "category" => (isset($a->categoryDetail) ? $a->categoryDetail->name : (($a->isVideo) ? "Videos" : "")),
                    "image" => "<div class='text-center'><img src='" . "../" . $image . "' class='article_image' alt='" . $a->heading . "'/></div>",
                    "view_count" => ($a->viewCount != "" ? $a->viewCount : 0),
                    "publish_date" => Carbon::parse($a->publishDate)->format("Y-m-d H:i:s"),
                    "action" => '<span class="editicn"><a href="' . (url("edit-article") . "/" . $a->id) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span><span class="deleteicn"><a onclick="' . $delete_onclick . '"><i class="fa fa-trash" aria-hidden="true"></i></a></span>' . $publish_button,
                ));
            }
            return json_encode(array(
                "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                "data" => $article_data_array,
                "recordsTotal" => $total_articles,
                "recordsFiltered" => $total_articles,
            ));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load unpublished article list
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function loadUnpublishedArticleList(Request $request)
    {
        try {
            return view("article.unpublishedArticlelist");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load unpublished article list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unpublishedArticleListPaginationData(Request $request)
    {
        try {
            $start = \Request::input('start');
            $end = \Request::input('length');
            $search_value = \Request::input('search')['value'];
            $search_value = strtolower($search_value);

            $order_detail = \Request::input('order');
            $sort_order = "DESC";
            $sort_column_name = "publishDate";
            $sort_columns = array("1" => "article_details.heading", "2" => "category_details.name", "4" => "article_details.publishDate");
            if (count((array)$order_detail) > 0) {
                $sort_column_name = $sort_columns[$order_detail[0]['column']];
                $sort_order = strtoupper($order_detail[0]['dir']);
            }
            $article_data = ArticleDetail::selectRaw("article_details.id,article_details.heading,article_details.categoryId,article_details.publishDate,article_details.status,article_details.isVideo")->leftJoin("category_details", function ($join) {
                $join->on('article_details.categoryId', '=', 'category_details.id');
            })->where("article_details.status", 0);

            if ($sort_column_name != "category") {
                $article_data = $article_data->orderBy($sort_column_name, $sort_order);
            }
            if ($search_value != "") {
                $article_data = $article_data->whereRaw("(article_details.heading like '%" . $search_value . "%' OR category_details.name like '%" . $search_value . "%')");
            }
            $total_articles = $article_data->count();
            $article_data = $article_data->skip($start)->take($end)->get();

            $article_data_array = array();
            foreach (count((array)$article_data) > 0 ? $article_data : array() as $a) {
                $publish_button = "";
                $onclick = "window.location='" . url("publish-article" . "/" . $a->id) . "'";
                $publish_button = '<span style="cursor:default"><input type="submit" onclick="' . $onclick . '" class="publish_button" value="Publish"/></span>';

                $main_image_data = $a->articleFileDetail->where("isMain", 1)->first();
                $image = "";
                if (!$a->isVideo) {
                    if ($main_image_data->type == "image") {
                        $image = ("uploads/article/" . $main_image_data->fileName);
                        if (!file_exists(public_path($image))) {
                            $image = ("images/image-not-found.png");
                        }
                    } else {
                        $image = ("images/Video.png");
                    }
                } else {
                    $image = ("images/Video.png");
                }
                array_push($article_data_array, array(
                    "checkbox" => "<input type='checkbox' value='" . $a->id . "' class='delete_entity'/>",
                    "heading" => $a->heading,
                    "category" => (isset($a->categoryDetail) ? $a->categoryDetail->name : (($a->isVideo) ? "Videos" : "")),
                    "image" => "<div class='text-center'><img src='" . '../' . $image . "' class='article_image' alt='" . $a->heading . "'/></div>",
                    "publish_date" => Carbon::parse($a->publishDate)->format("Y-m-d H:i:s"),
                    "action" => '<span class="editicn"><a href="' . (url("edit-article") . "/" . $a->id) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span><span class="deleteicn"><a onclick="javascript:deleteArticle(' . $a->id . ');"><i class="fa fa-trash" aria-hidden="true"></i></a></span>' . $publish_button,
                ));
            }
            return json_encode(array(
                "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                "data" => $article_data_array,
                "recordsTotal" => $total_articles,
                "recordsFiltered" => $total_articles,
            ));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load add article view
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function loadAddArticle($is_video)
    {
        try {
            $add_data = $this->getAddArticleData();
            $category_data_array = $add_data["category_data_array"];
            $feature_array = $add_data["feature_array"];
            $author_array = $add_data["author_array"];

            $title = ($is_video ? "Video" : "Article");
            return view("article.add", compact("title", "category_data_array", "feature_array", "author_array", "is_video"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function getAddArticleData()
    {
        $feature_array = array();
        $feature_data = ArticleFeatureDetail::selectRaw("id,name")->get();
        foreach (count((array)$feature_data) > 0 ? $feature_data : array() as $f) {
            $feature_array[$f->id] = $f->name;
        }
        $author_array = array();
        $author_data = AuthorDetail::selectRaw("id,name")->where("status", 1)->orderBy("name")->get();
        foreach (count((array)$author_data) > 0 ? $author_data : array() as $a) {
            $author_array[$a->id] = $a->name;
        }

        $category_data = CategoryDetail::whereIn("name", array("Community", "Canada", "International"))->orderBy("id")->get();
        $category_data_array = array();

        foreach (count((array)$category_data) > 0 ? $category_data : array() as $c) {
            $sub_category_array = array();
            $sub_category_data = CategoryDetail::selectRaw("id,name,parentId,level")->where("level", 1)->where("isDisplayInFrontend", 1)->where("parentId", $c->id)->orderBy("id")->get();
            foreach (count((array)$sub_category_data) > 0 ? $sub_category_data : array() as $sc) {
                $sub_category_array[$sc->id] = array("name" => $sc->name, "parent_id" => $sc->parentId, "level" => $sc->level, "child_list" => array());
            }
            $category_data_array[$c->id] = array("name" => $c->name, "parent_id" => $c->parentId, "level" => $c->level, "child_list" => $sub_category_array);
        }
        return array("feature_array" => $feature_array, "author_array" => $author_array, "category_data_array" => $category_data_array);
    }

    /**
     *  Method to load add article view
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function insertArticle(Request $request)
    {
        try {
            $element_array = array(
                'heading' => 'required',
                'description' => 'required',
                'summary' => ['required', function ($attribute, $value, $fail) {
                    if (str_word_count($value) > 100) {
                        return $fail("Please enter 100 word summary");
                    }
                }],
                'authorId' => 'required',
                'categoryId' => 'required'
            );
            $allMediaFiles = $request->file("allMediaFiles");
            $article_id = $request->input("article_id");
            if ($article_id == null) {
                $data = new ArticleDetail();
            } else {
                $data = ArticleDetail::find($article_id);
            }
            $deleted_ids = $request->input("deleted_ids");
            $isMainSelected = $request->input("isMainSelected");
            if ($article_id == null) {
                if (!isset($allMediaFiles)) {
                    $element_array['allMediaFiles'] = 'required';
                } else {
                    $element_array['isMainSelected'] = 'required';
                }
            } else {
                if ($deleted_ids != null) {
                    if (count(explode(",", $deleted_ids)) == count((array)$data->articleFileDetail)) {
                        $element_array['allMediaFiles'] = 'required';
                    }
                }
                if ($isMainSelected == "") {
                    $element_array['isMainSelected'] = 'required';
                }
            }

            $validator = Validator::make($request->all(), $element_array, [
                'heading.required' => 'Please enter heading',
                'description.required' => 'Please enter description',
                'summary.required' => 'Please enter summary',
                'authorId.required' => 'Please enter author id',
                'categoryId.required' => 'Please enter category id',
                'allMediaFiles.required' => 'Please select media(s)',
                'isMainSelected.required' => 'Please select main media to display'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }

            $data->description = $request->input("description");
            $data->summary = $request->input("summary");
            $data->heading = $request->input("heading");
            if ($request->input("featureId") != null) {
                $data->featureId = implode(",", $request->input("featureId"));
            }
            $data->authorId = $request->input("authorId");
            $data->categoryId = $request->input("categoryId")[0];
            if ($article_id == null) {
                $data->publishDate = Carbon::now()->format("Y-m-d H:i:s");
            }

            if ($request->input("expiryDate") != null) {
                $data->expiryDate = Carbon::parse($request->input("expiryDate"))->format("Y-m-d 23:59:59");
            } else {
                $data->expiryDate = null;
            }
            $data->isVideo = 0;
            $data->source = ($request->input("source") ?? "");
            $data->metaTag = ($request->input("metaTag") ? $request->input("metaTag") : "");
            if ($request->input("viewCount") != "") {
                $data->viewCount = $request->input("viewCount");
            }
            if ($article_id == null) {
                $data->status = ($request->input("isSaveDraft") ?? 1);
            }
            $data->save();

            $destination_path = public_path("uploads/article");
            if (!File::exists($destination_path)) {
                File::makeDirectory($destination_path);
            }

            $videoData = $request->input("videoData");


            if ($deleted_ids != null) {
                $deleted_ids_array = explode(",", $deleted_ids);
                foreach (count($deleted_ids_array) > 0 ? $deleted_ids_array : array() as $d) {
                    $file = ArticleFileDetail::where("id", $d)->where("articleId", $data->id)->first();
                    if ($file) {
                        if ($file->fileName != null && file_exists(public_path("uploads/article/" . $file->fileName))) {
                            unlink(public_path("uploads/article/" . $file->fileName));
                        }
                        if ($file->thumbnailImage != null && file_exists(public_path("uploads/video_thumbnail/" . $file->thumbnailImage))) {
                            unlink(public_path("uploads/video_thumbnail/" . $file->thumbnailImage));
                        }
                        $file->delete();
                    }
                }
            }
            $is_main_assigned = 0;
            $credit_data = $request->input("credit");
            $caption_data = $request->input("caption");
            foreach ((!empty($allMediaFiles) && count($allMediaFiles) > 0) ? $allMediaFiles : array() as $key => $value) {
                $article_file = new ArticleFileDetail();
                $article_file->articleId = $data->id;
                $article_file->save();

                $counter = $article_file->id;

                $file_name = $value->getClientOriginalName();
                $value->move($destination_path, $file_name);
                $article_file->fileName = $file_name;

                if (in_array($value->getClientMimeType(), array("image/gif", "image/jpeg", "image/jpg", "image/png"))) {
                    $article_file->type = "image";
                } else {
                    $article_file->type = "video";
                    if (isset($videoData[$key])) {
                        $video_data = json_decode(json_decode($videoData[$key]), true);
                        $thumbnail_img = str_replace('data:image/png;base64,', '', $video_data["thumbnail"]);
                        $thumbnail_img = str_replace(' ', '+', $thumbnail_img);
                        $thumbnail_data = base64_decode($thumbnail_img);
                        $thumbnail_file_name = $data->id . '_' . $counter . '.jpg';

                        if (file_put_contents((public_path('uploads/video_thumbnail') . "/" . $thumbnail_file_name), $thumbnail_data)) {
                            $article_file->thumbnailImage = $thumbnail_file_name;
                        }
                        $article_file->duration = $video_data["duration"];
                    }
                }
                if ($key == intval($isMainSelected)) {
                    $article_file->isMain = 1;
                    $is_main_assigned = 1;
                }
                if (isset($caption_data[$key])) {
                    $article_file->caption = $caption_data[$key];
                    unset($caption_data[$key]);
                }
                if (isset($credit_data[$key])) {
                    $article_file->credit = $credit_data[$key];
                    unset($credit_data[$key]);
                }
                $article_file->save();
            }
            if ($article_id != null && $isMainSelected != "" && !$is_main_assigned) {
                $article_file_detail = ArticleFileDetail::where("articleId", $data->id)->get();
                foreach (count((array)$article_file_detail) > 0 ? $article_file_detail : array() as $f) {
                    if ($f->id == intval($isMainSelected)) {
                        $f->isMain = 1;
                    } else {
                        $f->isMain = 0;
                    }
                    $f->save();
                }
            }
            if ($article_id != null) {
                foreach ((!empty($caption_data) && count($caption_data) > 0) ? $caption_data : array() as $key => $c) {
                    if ($c != null) {
                        $file_data = ArticleFileDetail::where("articleId", $data->id)->where("id", $key)->first();
                        if ($file_data) {
                            $file_data->caption = $c;
                            $file_data->save();
                        }
                    }
                }
                foreach ((!empty($credit_data) && count($credit_data) > 0) ? $credit_data : array() as $key => $c) {
                    if ($c != null) {
                        $file_data = ArticleFileDetail::where("articleId", $data->id)->where("id", $key)->first();
                        if ($file_data) {
                            $file_data->credit = $c;
                            $file_data->save();
                        }
                    }
                }
            }
            if ($data->status == 1 && intval($request->input("sendNotification"))) {
                $child_category_name = $data->categoryDetail->name;
                sendNotification($data->heading, (string)$data->id, $child_category_name, "article", $data->categoryId);
                sendNotificationAndroid($data->heading, (string)$data->id, $child_category_name, "article", $data->categoryId, $data->summary);
            }
            $msg = ("Article " . ($article_id == null ? "inserted" : "updated") . " successfully");
            \Request::session()->flash('success', $msg);
            return response()->json(array("list" => (($data->status) ? url("article-list") : url("unpublished-article-list"))));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load add article view
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function insertVideo(Request $request)
    {
        try {
            $element_array = array(
                'heading' => 'required',
                'description' => 'required',
                'summary' => 'required',
                'authorId' => 'required'
            );
            $article_id = $request->input("article_id");
            $video_file = $request->file("videoFile");
            $youtube_url = $request->input("youtubeUrl");
            $thumbnail_image = $request->file("videoThumbnail");
            if ($article_id == null && !isset($video_file) && $youtube_url == "") {
                $element_array['videoFile'] = 'required';
                $element_array['youtubeUrl'] = 'required';
            } else {
                if (isset($video_file)) {
                    $element_array['videoFile'] = 'mimetypes:video/mp4,video/avi,video/mov,video/3gp,video/ogg,video/quicktime';
                } else if ($youtube_url != "") {
                    if (!isset($thumbnail_image) && $article_id == null) {
                        $element_array['videoThumbnail'] = 'required';
                    } else {
                        if ($request->input("youtube_duration") == "") {
                            $element_array['youtubeUrl'] = 'required';
                        }
                        if (isset($thumbnail_image)) {
                            $element_array['videoThumbnail'] = 'image|mimes:jpg,jpeg';
                        }
                    }
                }
            }
            $validator = Validator::make($request->all(), $element_array, [
                'heading.required' => 'Please enter heading',
                'description.required' => 'Please enter description',
                'summary.required' => 'Please enter summary',
                'authorId.required' => 'Please enter author id',
                'videoFile.required' => 'Please select video file',
                'youtubeUrl.required' => 'Please enter valid youtube url',
                'videoThumbnail.required' => 'Please select thumbnail image for youtube'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if ($article_id == null) {
                $data = new ArticleDetail();
            } else {
                $data = ArticleDetail::find($article_id);
            }
            $data->description = $request->input("description");
            $data->summary = $request->input("summary");
            $data->heading = $request->input("heading");
            $data->authorId = $request->input("authorId");
            if ($article_id == null) {
                $data->publishDate = Carbon::now()->format("Y-m-d H:i:s");
            }
            if ($request->input("expiryDate") != null) {
                $data->expiryDate = Carbon::parse($request->input("expiryDate"))->format("Y-m-d 23:59:59");
            } else {
                $data->expiryDate = null;
            }
            $data->videoCredit = ($request->input("videoCredit") ?? "");
            $data->isVideo = 1;
            $data->isYoutubeVideo = 0;
            if ($youtube_url != "") {
                $data->youtubeUrl = $youtube_url;
                $data->isYoutubeVideo = 1;
            }
            $data->source = ($request->input("source") ?? "");
            $data->metaTag = ($request->input("metaTag") ? $request->input("metaTag") : "");
            if ($request->input("viewCount") != "") {
                $data->viewCount = $request->input("viewCount");
            }
            if ($article_id == null) {
                $data->status = ($request->input("isSaveDraft") ?? 1);
            }
            $data->save();

            $destination_path = public_path("uploads/video");
            if (!File::exists($destination_path)) {
                File::makeDirectory($destination_path);
            }
            if (isset($video_file)) {
                $video_file_name = $video_file->getClientOriginalName();
                $video_file->move($destination_path, $video_file_name);

                $data->videoDuration = $request->input("video_duration");
                $data->videoFile = $video_file_name;
                $thumbnail_img = $request->input("thumbnail_image");
                if (isset($thumbnail_img)) {
                    $thumbnail_img = str_replace('data:image/png;base64,', '', $thumbnail_img);
                    $thumbnail_img = str_replace(' ', '+', $thumbnail_img);
                    $thumbnail_data = base64_decode($thumbnail_img);
                    $file_name = $data->id . '.jpg';

                    if (file_put_contents((public_path('uploads/video_thumbnail') . "/" . $file_name), $thumbnail_data)) {
                        $data->videoThumbnail = $file_name;
                    }
                }
                if ($data->youtubeUrl != null) {
                    $data->youtubeUrl = "";
                    $data->isYoutubeVideo = 0;
                }
                $data->save();
            } else if ($data->isYoutubeVideo) {
                if (isset($thumbnail_image)) {
                    if ($data->videoFile != null) {
                        unlink(public_path("uploads/video/" . $data->videoFile));
                        unlink(public_path("uploads/video_thumbnail/" . $data->videoThumbnail));
                        $data->videoFile = "";
                    }
                    $video_thumbnail_image = $thumbnail_image->getClientOriginalName();
                    $thumbnail_image->move(public_path('uploads/video_thumbnail'), $video_thumbnail_image);
                    $data->videoThumbnail = $video_thumbnail_image;
                }
                $data->videoDuration = $request->input("youtube_duration");
                $data->save();
            }
            if ($data->status == 1 && intval($request->input("sendNotification"))) {
                $video_category_id = 0;
                $video_category_data = CategoryDetail::select("id")->where("name", "Videos")->first();
                if ($video_category_data) {
                    $video_category_id = $video_category_data->id;
                }
                sendNotification($data->heading, (string)$data->id, "VIDEOS", "video", $video_category_id);
                sendNotificationAndroid("Video", (string)$data->id, "VIDEOS", "video", $video_category_id, $data->heading);
            }
            return redirect()->route(($data->status) ? "article-list" : "unpublished-article-list")->with("success", "Video " . ($article_id == null ? "inserted" : "updated") . " successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Function to get Youtube video data
     */
    public function getYoutubeData(Request $request)
    {
        $video_id = request("video_id");
        $youtube_data = getYoutubeData($video_id);

        return (count($youtube_data) > 0 ? json_encode($youtube_data) : "");
    }

    /**
     *  Method to edit article
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function editArticle($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $article_data = ArticleDetail::where("id", $id)->first();
            if (!$article_data) {
                return view("errors.404");
            }
            $add_data = $this->getAddArticleData();
            $category_data_array = $add_data["category_data_array"];
            $feature_array = $add_data["feature_array"];
            $author_array = $add_data["author_array"];
            
            $is_video = (intval($article_data->isVideo) ? $article_data->isVideo : 0);
            $title = ($is_video ? "Video" : "Article");
            // print_r($is_video);die;
            if ($article_data->expiryDate != "") {
                $article_data->expiryDate = Carbon::parse($article_data->expiryDate)->format("Y-m-d");
            }
            $article_data->featureId = explode(",", $article_data->featureId);
            $file_data = $article_data->articleFileDetail;
            // echo"123";die;
            return view("article.add", compact("category_data_array", "feature_array", "author_array", "article_data", "title", "is_video", "file_data"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to delete article
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteArticle($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $article_data = ArticleDetail::find($id);
            if (!$article_data) {
                return view("errors.404");
            }
            $is_video = intval($article_data->isVideo);
            $this->deleteArticleDetails($article_data);
            return redirect()->route(($article_data->status) ? "article-list" : "unpublished-article-list")->with("success", ($is_video ? "Video" : "Article") . " deleted successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function deleteArticleDetails($article_data)
    {
        $is_video = intval($article_data->isVideo);
        if ($is_video) {
            if ($article_data->videoFile != null && file_exists(public_path("uploads/video/" . $article_data->videoFile))) {
                unlink(public_path("uploads/video/" . $article_data->videoFile));
            }
            if ($article_data->videoThumbnail != null && file_exists(public_path("uploads/video_thumbnail/" . $article_data->videoThumbnail))) {
                unlink(public_path("uploads/video_thumbnail/" . $article_data->videoThumbnail));
            }
        } else {
            foreach (count((array)$article_data->articleFileDetail) > 0 ? $article_data->articleFileDetail : array() as $f) {
                if ($f->fileName != null && file_exists(public_path("uploads/article/" . $f->fileName))) {
                    unlink(public_path("uploads/article/" . $f->fileName));
                }
                if ($f->thumbnailImage != null && file_exists(public_path("uploads/video_thumbnail/" . $f->thumbnailImage))) {
                    unlink(public_path("uploads/video_thumbnail/" . $f->thumbnailImage));
                }
                $f->delete();
            }
        }
        $article_data->delete();
    }

    /**
     *  Method to delete article
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteArticles()
    {
        try {
            $ids_to_delete = request("article_ids");
            if ($ids_to_delete == "") {
                return view("errors.403");
            }
            $article_ids = explode(",", $ids_to_delete);
            foreach (count($article_ids) > 0 ? $article_ids : array() as $a) {
                $article_data = ArticleDetail::find($a);
                if ($article_data) {
                    $this->deleteArticleDetails($article_data);
                }
            }
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to publish article
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function publishArticle($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $article_data = ArticleDetail::find($id);
            if (!$article_data) {
                return view("errors.404");
            }
            $article_data->status = 1;
            $article_data->save();
            return redirect()->route("article-list")->with("success", "Article published successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to unpublish article
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unpublishArticle($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $article_data = ArticleDetail::find($id);
            if (!$article_data) {
                return view("errors.404");
            }
            $article_data->status = 0;
            $article_data->save();
            return redirect()->route("article-list")->with("success", "Article unpublished successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to get article tags
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMetaTags(Request $request)
    {
        try {
            $tags = $result = array();
            $search_string = $request->input("term");
            if ($search_string == "") {
                return [];
            }
            $all_tags = ArticleDetail::select("metaTag")->whereRaw("metaTag != ''");
            if ($search_string != "") {
                $search_string = strtolower($search_string);
                $all_tags = $all_tags->whereRaw("LOWER(metaTag) like '%" . addslashes($search_string) . "%'");
            }
            $all_tags = $all_tags->take(50)->orderBy("id", "desc")->get();
            foreach (count((array)$all_tags) > 0 ? $all_tags : array() as $t) {
                $seperated_tags = explode(";", $t->metaTag);
                foreach (count($seperated_tags) > 0 ? $seperated_tags : array() as $s) {
                    $s = trim($s);
                    if (!in_array($s, $tags)) {
                        $is_insert = false;
                        if ($search_string != "") {
                            if (stripos($s, $search_string) > -1)
                                $is_insert = true;
                        } else {
                            $is_insert = true;
                        }
                        if ($is_insert) {
                            array_push($tags, $s);
                            array_push($result, $s);
                        }
                    }
                }
            }
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}

