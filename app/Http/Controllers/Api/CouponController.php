<?php

namespace App\Http\Controllers\Api;

use App\Models\CouponDetail;
use App\Models\CategoryDetail;
use Carbon\Carbon;


class CouponController extends Controller
{
    /**
     *  Method to get category wise coupan data
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function getCouponListByCategory()
    {
        try {
            $category_id = request("category_id");
            if ($category_id == "") {
                return $this->sendResultJSON("0", "Please enter category id");
            }
            $page = (int)request("active_page");
            $total_records = (int)request("total_records");

            $data = CouponDetail::selectRaw("id,name,heading,categoryId,thumbnailImage,originalPrice,discountPrice,offerDetail")->where("status", 1);
            $get_All_cat_detail = CategoryDetail::where("isCouponCategory", 1)->where("id", $category_id)->first();
            if ($get_All_cat_detail) {
                if ($get_All_cat_detail->name != "All") {
                    $data = $data->where("categoryId", $category_id);
                }
                $all_data_count = $data->count();
                $data = $data->skip(($page - 1) * $total_records)->take($total_records)->get();
                $coupon_array = array();
                foreach (count((array)$data) > 0 ? $data : array() as $d) {
                    array_push($coupon_array, $this->iterate_coupon_data($d, 1));
                }
                return $this->sendResultJSON("1", (count($coupon_array) > 0 ? "" : "No coupon(s) found."), array("coupon_data" => $coupon_array, "total_records" => $all_data_count));
            }
            return $this->sendResultJSON("0", "No coupon category found");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to iterate category data
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    private function iterate_coupon_data($coupon, $is_list = 1)
    {
        $coupon_detail = $coupon;
        $day_name_array = array("mon" => "monday", "tue" => "tuesday", "wed" => "wednesday", "thr" => "thursday", "fri" => "friday", "sat" => "saturday", "sun" => "sunday");
        if (!$is_list) {
            $coupon_detail->highlights = htmlspecialchars_decode($coupon_detail->highlights);
            $coupon_detail->finePrints = htmlspecialchars_decode($coupon_detail->finePrints);
            $coupon_detail->detail = htmlspecialchars_decode($coupon_detail->detail);
            $daywise_data = json_decode($coupon_detail->daywiseTime, true);
            $daywise_data_array = array();
            foreach ($daywise_data as $k => $v) {
                if ($v != "") {
                    $time_value = explode("-", $v);
                    $daywise_data[$k] = Carbon::parse($time_value[0])->format("h:i A") . " - " . Carbon::parse($time_value[1])->format("h:i A");
                } else {
                    $daywise_data[$k] = "";
                }
                array_push($daywise_data_array, array("day" => ucfirst($day_name_array[$k]), "timing" => $daywise_data[$k]));
            }
            $coupon_detail->daywiseTime = $daywise_data_array;
            $banner_images = json_decode($coupon->bannerImage,true);
            $banner_image_array = array();
            foreach(count($banner_images) > 0 ? $banner_images : array() as $b){
                array_push($banner_image_array,url($b));
            }
            $coupon->bannerImage = $banner_image_array[0];
            $coupon->allBannerImages = $banner_image_array;
        }
        $coupon->thumbnailImage = url($coupon->thumbnailImage);
        $less =  $coupon_detail->originalPrice - $coupon_detail->discountPrice;
        $discount_price = (100 * $less) / $coupon_detail->originalPrice;
        $coupon_detail->offerDetail = (empty($coupon_detail->offerDetail) ? (round($discount_price) . "% OFF") : $coupon_detail->offerDetail);
        unset($coupon_detail->created_at, $coupon_detail->updated_at, $coupon->deleted_at, $coupon->status);
        $coupon_detail->originalPrice = "C$" . $coupon_detail->originalPrice;
        $coupon_detail->discountPrice = "C$" . $coupon_detail->discountPrice;
        return $coupon_detail;
    }

    /**
     *  Method to get category wise coupan data
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function getCouponById($coupon_id)
    {
        try {
            if ($coupon_id == "") {
                return $this->sendResultJSON("0", "Please enter coupon id");
            }
            $data = CouponDetail::where("id", $coupon_id)->where("status", 1)->first();
            $coupon_array = array();
            if ($data) {
                $coupon_array = $this->iterate_coupon_data($data, 0);
                return $this->sendResultJSON("1", "", array("coupon_data" => $coupon_array));
            }
            return $this->sendResultJSON("0", "No data found");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
}
