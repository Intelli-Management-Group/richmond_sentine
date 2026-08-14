<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdvertisementDetail;
use App\Models\AdvertisementFileDetail;
use App\Models\ArticleDetail;
use App\Models\CategoryDetail;
use App\Models\CouponDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\File;

class CouponzoneController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $coupon_category = CategoryDetail::select("id", "name")->where('isCouponCategory', 1)->where('name', '!=', 'All')->get();

        $coupon_category_id = 0;
        $coupon_category_data = CategoryDetail::select("id")->where("name", "Couponzone")->first();
        if ($coupon_category_data) {
            $coupon_category_id = $coupon_category_data->id;
        }
        $ad_bottom = getAdvertisementData("bottom", $coupon_category_id);

        $category_id_community = get_category_id("Community");
        $sub_category_data = CategoryDetail::select("id")->where("parentId", $category_id_community)->where('isDisplayInFrontend', 1)->get();
        $category_ids = array();
        foreach (count((array)$sub_category_data) > 0 ? $sub_category_data : array() as $sc) {
            array_push($category_ids, $sc->id);
        }
        $community_news = getArticlelist($category_ids, 10, array());
        $category_id_business = get_category_id("Business");
        $business_news = getArticlelist(array($category_id_business), 10, array());
        $category_id_international = get_category_id("International");
        $international_news = getArticlelist(array($category_id_international), 10, array());
        $category_id_sports = get_category_id("Sports");
        $sports_news = getArticlelist(array($category_id_sports), 10, array());
        return view('frontend.couponzone', compact('coupon_category', 'ad_bottom', 'category_id_community', 'community_news', 'category_id_business', 'business_news', 'category_id_international', 'international_news', 'category_id_sports', 'sports_news'));
    }

    public function getBody()
    {
        $id = request("id");
        $coupons = CouponDetail::select("id", "heading", "companyName", "thumbnailImage", "originalPrice", "discountPrice");
        if ($id != "all") {
            $coupons = $coupons->where('categoryId', $id);
        }

        $coupons = $coupons->orderByRaw('publishDate DESC')->take(10)->get();
        $view = view("frontend.couponzoneajax", compact('coupons'))->render();

        return response()->json(['html' => $view]);
    }

    public function coupondetail($id)
    {

        $coupons_detail = CouponDetail::select('heading', 'highlights','finePrints', 'companyName', 'detail', 'companyAddress', 'companyPhone', 'bannerImage', 'daywiseTime')->where('id', $id)->first();
        $banner_images = json_decode($coupons_detail->bannerImage, true);
        $day_name_array = array("mon" => "monday", "tue" => "tuesday", "wed" => "wednesday", "thr" => "thursday", "fri" => "friday", "sat" => "saturday", "sun" => "sunday");
        $daywise_data = json_decode($coupons_detail->daywiseTime, true);

        $coupon_category_id = 0;
        $coupon_category_data = CategoryDetail::select("id")->where("name", "Couponzone")->first();
        if ($coupon_category_data) {
            $coupon_category_id = $coupon_category_data->id;
        }

        $ad_sidebar = getAdvertisementData("sidebar", $coupon_category_id);
        $ad_bottom = getAdvertisementData("bottom", $coupon_category_id);
        $trending = getArticleDatahome([], 200, "false", "false", "true");

        return view("frontend.coupondetail", compact('coupons_detail', 'banner_images', 'daywise_data', 'ad_sidebar', 'ad_bottom', 'trending'));
    }

    /**
     *  Method to load coupon list
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function loadCouponList()
    {
        try {
            return view("coupon.list");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load coupon pagination
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function couponPaginationData(Request $request)
    {
        try {
            $start = \Request::input('start');
            $end = \Request::input('length');
            $search_value = \Request::input('search')['value'];
            $search_value = strtolower($search_value);

            $order_detail = \Request::input('order');
            $sort_order = "DESC";
            $sort_column_name = "publishDate";
            $sort_columns = array("1" => "name", "2" => "heading", "4" => "companyName", "6" => "publishDate");
            if (count((array)$order_detail) > 0) {
                $sort_column_name = $sort_columns[$order_detail[0]['column']];
                $sort_order = strtoupper($order_detail[0]['dir']);
            }
            $coupon_data = CouponDetail::whereRaw(1);
            if ($search_value != "") {
                $coupon_data = $coupon_data->whereRaw("(name like '%" . $search_value . "%' OR heading like '%" . $search_value . "%' OR companyName like '%" . $search_value . "%')");
            }
            $total_coupons = $coupon_data->count();
            $coupon_data = $coupon_data->skip($start)->take($end)->orderBy($sort_column_name, $sort_order)->get();

            $coupon_data_array = array();
            foreach (count((array)$coupon_data) > 0 ? $coupon_data : array() as $c) {
                $publish_button = "";
                // $onclick = "window.location='" . url("publish-article" . "/" . $a->id) . "'";
                // $publish_button = '<span style="cursor:default"><input type="submit" onclick="' . $onclick . '" class="publish_button" value="Publish"/></span>';

                array_push($coupon_data_array, array(
                    "checkbox" => "<input type='checkbox' value='" . $c->id . "' class='delete_entity'/>",
                    "name" => $c->name,
                    "heading" => $c->heading,
                    "category" => $c->categoryDetail->name,
                    "company_name" => $c->companyName,
                    "image" => "<img src='" . "../".($c->thumbnailImage) . "' class='coupon_image' alt='" . $c->heading . "'/>",
                    "publish_date" => Carbon::parse($c->publishDate)->format("Y-m-d H:i:s"),
                    "action" => '<span class="editicn"><a href="' . (url("edit-coupon") . "/" . $c->id) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span><span class="deleteicn"><a onclick="javascript:deleteCoupon(' . $c->id . ')"><i class="fa fa-trash" aria-hidden="true"></i></a></span>' . $publish_button,
                ));
            }
            return json_encode(array(
                "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                "data" => $coupon_data_array,
                "recordsTotal" => $total_coupons,
                "recordsFiltered" => $total_coupons,
            ));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load add coupon view
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function loadAddCoupon()
    {
        try {
            $coupon_category_array = array();
            $coupon_category_data = CategoryDetail::selectRaw("id,name")->where("isCouponCategory", 1)->get();
            foreach (count((array)$coupon_category_data) > 0 ? $coupon_category_data : array() as $c) {
                $coupon_category_array[$c->id] = $c->name;
            }
            $day_name_array = array("mon" => "monday", "tue" => "tuesday", "wed" => "wednesday", "thr" => "thursday", "fri" => "friday", "sat" => "saturday", "sun" => "sunday");
            return view("coupon.add", compact("day_name_array", "coupon_category_array"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     *  Method to insert coupon data
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function insertCoupon(Request $request)
    {
        try {
            $element_array = array(
                'name' => 'required',
                'heading' => 'required',
                'categoryId' => 'required',
                'highlights' => 'required',
                'finePrints' => 'required',
                'detail' => 'required',
                'companyName' => 'required',
                'companyAddress' => 'required',
                'originalPrice' => 'required',
                'discountPrice' => 'required'
            );
            $start_time = $request->input("starttime");
            $end_time = $request->input("endtime");
            if (count(array_filter(array_values($start_time)))  == 0 && count(array_filter(array_values($end_time))) == 0) {
                $element_array['coupontime'] = 'required';
            }
            if (count(array_filter(array_values($start_time))) != count(array_filter(array_values($end_time)))) {
                $element_array['coupontime'] =  ['required', function ($attribute, $value, $fail) {
                    return $fail("Please enter start time and end time");
                }];
            }
            $coupon_id = $request->input("coupon_id");
            $deleted_ids = $request->input("deleted_ids");
            $existing_banner_images = array();
            if ($coupon_id == null) {
                $coupon = new CouponDetail();
            } else {
                $coupon = CouponDetail::find($coupon_id);
                $existing_banner_images = json_decode($coupon->bannerImage, true);
            }
            if ($coupon_id == null) {
                $element_array['bannerImage'] = 'required';
                $element_array['thumbnailImage'] = 'required';
            }
            $banner_images = $request->file("bannerImage");
            $thumbnailImage = $request->file("thumbnailImage");
            if ($coupon_id == null && isset($banner_images) && isset($thumbnailImage)) {
                $element_array['bannerImage.*'] = 'image|mimes:png,jpg,jpeg,gif';
                $element_array['thumbnailImage'] = 'image|mimes:png,jpg,jpeg,gif';
            } else {
                if ($deleted_ids != null) {
                    if (count($existing_banner_images) == count(explode(",", $deleted_ids))) {
                        $element_array['bannerImage'] = 'required';
                    }
                }
            }

            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter coupon name',
                'heading.required' => 'Please enter heading',
                'categoryId.required' => 'Please select category',
                'highlights.required' => 'Please enter coupon highlights',
                'finePrints.required' => 'Please enter coupon fineprint',
                'detail.required' => 'Please enter coupon detail',
                'companyName.required' => 'Please enter company name',
                'companyAddress.required' => 'Please enter company address',
                'originalPrice.required' => 'Please enter original price',
                'discountPrice.required' => 'Please enter discount price',
                'bannerImage.required' => 'Please select banner images',
                'coupontime.required' => 'Please enter start time and end time',
                'thumbnailImage.required' => 'Please select thumbnail image'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }

            $coupon->name = $request->input("name");
            $coupon->heading = $request->input("heading");
            $coupon->categoryId = $request->input("categoryId");
            $coupon->highlights = $request->input("highlights");
            $coupon->finePrints = $request->input("finePrints");
            $coupon->detail = $request->input("detail");
            $coupon->companyName = $request->input("companyName");
            $coupon->companyPhone = $request->input("companyPhone") ?? "";
            $coupon->companyEmail = $request->input("companyEmail") ?? "";
            $coupon->companyAddress = $request->input("companyAddress") ?? "";
            $coupon->originalPrice = $request->input("originalPrice");
            $coupon->discountPrice = $request->input("discountPrice");
            $coupon->publishDate = Carbon::now()->format("Y-m-d H:i:s");
            $coupon->offerDetail = $request->input("offerDetail") ?? "";
            $coupon->status = 1;

            $daywise_data = array();
            $day_name_array = array("mon", "tue", "wed", "thr", "fri", "sat", "sun");
            foreach ($day_name_array as $d) {
                if (isset($start_time[$d]) && isset($end_time[$d])) {
                    $daywise_data[$d] = $start_time[$d] . "-" . $end_time[$d];
                } else {
                    $daywise_data[$d] = "";
                }
            }
            $coupon->daywiseTime = json_encode($daywise_data);
            $coupon->save();
            $destination_path = public_path("uploads/coupon/coupon_images");
            if (!File::exists($destination_path)) {
                File::makeDirectory($destination_path);
            }

            if ($deleted_ids != null && count($existing_banner_images) > 0) {
                $deleted_id_array = explode(",", $deleted_ids);
                foreach (count($deleted_id_array) > 0 ? $deleted_id_array : array() as $d) {
                    if (file_exists(public_path($existing_banner_images[$d]))) {
                        unlink(public_path($existing_banner_images[$d]));
                        unset($existing_banner_images[$d]);
                    }
                }
            }

            if (isset($banner_images) || isset($thumbnailImage)) {
                if (isset($banner_images)) {
                    foreach (count($banner_images) > 0 ? $banner_images : array() as $key => $b) {
                        $image_name = $coupon->id . ("_" . uniqid() . "_l.") . $b->getClientOriginalExtension();
                        $b->move($destination_path, $image_name);
                        array_push($existing_banner_images, ("uploads/coupon/coupon_images/" . $image_name));
                    }
                }
                if (isset($thumbnailImage)) {
                    $thumbnail_image_name = $coupon->id . "_s." . $thumbnailImage->getClientOriginalExtension();
                    $thumbnailImage->move($destination_path, $thumbnail_image_name);
                    $coupon->thumbnailImage = "uploads/coupon/coupon_images/" . $thumbnail_image_name;
                }
            }
            $coupon->bannerImage = json_encode($existing_banner_images, JSON_FORCE_OBJECT);
            $coupon->save();
            if ($coupon_id == null) {
                $coupon_category_id = 0;
                $coupon_category_data = CategoryDetail::select("id")->where("name", "Couponzone")->first();
                if ($coupon_category_data) {
                    $coupon_category_id = $coupon_category_data->id;
                }
                sendNotification($coupon->heading, (string)$coupon->id, ("COUPONZONE" . (isset($coupon->categoryDetail) ? (" > " . $coupon->categoryDetail->name) : "")), "coupon", $coupon_category_id);
                sendNotificationAndroid("Coupon", (string)$coupon->id, ("COUPONZONE" . (isset($coupon->categoryDetail) ? (" > " . $coupon->categoryDetail->name) : "")), "coupon", $coupon_category_id, $coupon->heading);
            }
            \Request::session()->flash('success', ("Coupon " . ($coupon_id == null ? "inserted" : "updated") . " successfully"));
            return response()->json(array("list" => url("coupon-list")));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load edit coupon view
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function loadEditCoupon($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $coupon_data = CouponDetail::where("id", $id)->first();
            if (!$coupon_data) {
                return view("errors.404");
            }
            if ($coupon_data->status == 0) {
                return view("errors.403");
            }
            $coupon_category_array = array();
            $coupon_category_data = CategoryDetail::selectRaw("id,name")->where("isCouponCategory", 1)->get();
            foreach (count((array)$coupon_category_data) > 0 ? $coupon_category_data : array() as $c) {
                $coupon_category_array[$c->id] = $c->name;
            }
            $day_name_array = array("mon" => "monday", "tue" => "tuesday", "wed" => "wednesday", "thr" => "thursday", "fri" => "friday", "sat" => "saturday", "sun" => "sunday");

            $start_time = array();
            $end_time = array();
            $coupon_daywise_data = json_decode($coupon_data->daywiseTime, true);

            foreach ($coupon_daywise_data as $key => $value) {
                $individual_day = (($value != "") ? explode("-", $value) : "");
                if (is_array($individual_day)) {
                    $start_time[$key] = $individual_day[0];
                    $end_time[$key] = $individual_day[1];
                } else {
                    $start_time[$key] = "";
                    $end_time[$key] = "";
                }
            }
            $coupon_data->starttime = $start_time;
            $coupon_data->endtime = $end_time;
            $coupon_data->banner_image_array = json_decode($coupon_data->bannerImage, true);
            return view("coupon.add", compact("day_name_array", "coupon_category_array", "coupon_data"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to delete coupon
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function deleteCoupon($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $coupon_data = CouponDetail::find($id);
            if (!$coupon_data) {
                return view("errors.404");
            }
            $banner_image_array = json_decode($coupon_data->bannerImage);
            foreach (count((array)$banner_image_array) > 0 ? $banner_image_array : array() as $b) {
                if (file_exists(public_path($b))) {
                    unlink(public_path($b));
                }
            }
            if ($coupon_data->thumbnailImage != null && file_exists(public_path($coupon_data->thumbnailImage))) {
                unlink(public_path($coupon_data->thumbnailImage));
            }
            $coupon_data->delete();
            return redirect()->route("coupon-list")->with("success", "Coupon deleted successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to delete coupons
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function deleteCoupons()
    {
        try {
            $ids_to_delete = request("coupon_ids");
            if ($ids_to_delete == "") {
                return view("errors.403");
            }
            $coupon_ids = explode(",", $ids_to_delete);
            foreach (count($coupon_ids) > 0 ? $coupon_ids : array() as $c) {
                $coupon_data = CouponDetail::find($c);
                if ($coupon_data) {
                    $banner_image_array = json_decode($coupon_data->bannerImage);
                    foreach (count((array)$banner_image_array) > 0 ? $banner_image_array : array() as $b) {
                        if (file_exists(public_path($b))) {
                            unlink(public_path($b));
                        }
                    }
                    if ($coupon_data->thumbnailImage != null && file_exists(public_path($coupon_data->thumbnailImage))) {
                        unlink(public_path($coupon_data->thumbnailImage));
                    }
                    $coupon_data->delete();
                }
            }
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
