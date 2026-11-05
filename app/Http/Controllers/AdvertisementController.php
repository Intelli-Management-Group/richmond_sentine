<?php

namespace App\Http\Controllers;

use App\Models\AdvertisementDetail;
use App\Models\AdvertisementFileDetail;
use App\Models\CategoryDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Validator;

class AdvertisementController extends Controller
{
    /**
     *  Method to load advertisement add form
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function loadAdvertisement(Request $request)
    {
        try {

            return view("advertisement.list");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method for pagination in advertisement list
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function advertisementPaginationData(Request $request)
    {
        try {
            $start = \Request::input('start');
            $end = \Request::input('length');

            $search_value = \Request::input('search')['value'];
            $search_value = strtolower($search_value);

            $order_detail = \Request::input('order');
            $sort_order = "DESC";
            if (is_array($order_detail) && count($order_detail) > 0) {
                $sort_order = strtoupper($order_detail[0]['dir']);
            }
            $advertisement_data = AdvertisementDetail::orderBy("publishDate", $sort_order);
            if ($search_value != "") {
                $advertisement_data = $advertisement_data->whereAny(["link", "name"], "like", "%" . $search_value . "%");
            }
            $total_ads = $advertisement_data->count();
            $advertisement_data = $advertisement_data->skip($start)->take($end)->get();

            $advertisement_data_array = array();
            foreach (count((array)$advertisement_data) > 0 ? $advertisement_data : array() as $a) {
                $publish_button = "";
                if ($a->status == 1) {
                    $onclick = "window.location='" . url("unpublish-advertisement/" . $a->id) . "'";
                    $publish_button = '<span style="cursor:default"><input type="submit" onclick="' . $onclick . '" class="publish_button" value="Unpublish"/></span>';
                } else {
                    $onclick = "window.location='" . url("publish-advertisement/" . $a->id) . "'";
                    $publish_button = '<span style="cursor:default"><input type="submit" class="publish_button" onclick="' . $onclick . '" value="Publish"/></span>';
                }
                $get_ad_file = (isset($a->advertisementFileDetail) ? ("uploads/advertisement/" . $a->advertisementFileDetail[0]->imageName) : "images/image-not-found.png");
                array_push($advertisement_data_array, array(
                    "checkbox" => "<input type='checkbox' value='" . $a->id . "' class='delete_entity'/>",
                    "name" => $a->name,
                    "link" => $a->link,
                    "publish_date" => Carbon::parse($a->publishDate)->format("Y-m-d H:i:s"),
                    "type" => config("constants.advertisement_type")[$a->type],
                    "image" => "<div class='text-center'><img src='" . ('../'.($get_ad_file)) . "' class='ad_image' alt='Richmond sentinel advertisement'/></div>",
                    "action" => '<span class="editicn"><a href="' . (url("edit-advertisement") . "/" . $a->id) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span><span class="deleteicn"><a onclick="javascript:deleteAdvertisement(' . $a->id . ')"><i class="fa fa-trash" aria-hidden="true"></i></a></span>' . $publish_button,
                ));
            }
            return json_encode(array(
                "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                "data" => $advertisement_data_array,
                "recordsTotal" => $total_ads,
                "recordsFiltered" => $total_ads,
            ));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load add advertisement view
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function loadAdverstisementView(Request $request)
    {
        try {
            $category_array = $this->loadAdvertisementCategory();
            $web_category_data_array = $category_array["web_category_data_array"];
            $app_category_data_array = $category_array["app_category_data_array"];

            return view("advertisement.add", compact("web_category_data_array", "app_category_data_array"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to insert advertisement
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function insertAdverstisement(Request $request)
    {
        try {

            $element_array = array(
                'name' => 'required',
                'link' => 'required',
                'advertisementFor' => 'required'
            );
            $advertisement_for = $request->input("advertisementFor");
            $web_position = $request->input("web_position");
            $app_position = $request->input("app_position");
            $image_category = $request->input("image_category");
            $image_array = $request->file("position_image");

            if (isset($advertisement_for)) {
                if (in_array("web", $advertisement_for)) {
                    if (!isset($web_position)) {
                        $element_array['web_position'] = 'required';
                    } else {
                        foreach (count($web_position) > 0 ? $web_position : array() as $wp) {
                            $category_key = $wp;
                            if ($wp == "sidebar_responsive") {
                                $category_key = "sidebar";
                            }
                            if (!isset($image_category) || (isset($image_category) && !isset($image_category[$category_key]))) {
                                $element_array['image_category.' . $category_key] = 'required';
                            }
                            if (!isset($image_array) || (isset($image_array) && !isset($image_array[$wp]))) {
                                $element_array['position_image.' . $wp] = 'required';
                            }
                        }
                    }
                }
                if (in_array("app", $advertisement_for)) {
                    if (!isset($app_position)) {
                        $element_array['app_position'] = 'required';
                    } else {
                        foreach (count($app_position) > 0 ? $app_position : array() as $ap) {
                            if (!isset($image_category) || (isset($image_category) && !isset($image_category[$ap]))) {
                                $element_array['image_category.' . $ap] = 'required';
                            }
                            if (!isset($image_array) || (isset($image_array) && !isset($image_array[$ap]))) {
                                $element_array['position_image.' . $ap] = 'required';
                            }
                        }
                    }
                }
            }
            if ($request->input("isLocationDetection")) {
                $element_array['address'] = 'required';
            }
            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter advertisement name',
                'link.required' => 'Please enter redirection link',
                'address.required' => 'Please enter address',
                'advertisementFor.required' => 'Please select where advertisement will display',
                'web_position.required' => 'Please select position for web',
                'app_position.required' => 'Please select position for app',
                'image_category.sidebar.required' => 'Please select category',
                'image_category.middle.required' => 'Please select category',
                'image_category.bottom.required' => 'Please select category',
                'image_category.square.required' => 'Please select category',
                'image_category.horizontal.required' => 'Please select category',
                'image_category.tablet_square.required' => 'Please select category',
                'position_image.sidebar.required' => 'Please select image',
                'position_image.sidebar_responsive.required' => 'Please select image',
                'position_image.middle.required' => 'Please select image',
                'position_image.bottom.required' => 'Please select image',
                'position_image.square.required' => 'Please select image',
                'position_image.horizontal.required' => 'Please select image',
                'position_image.tablet_square.required' => 'Please select image'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $address = "";
            if ($request->input("isLocationDetection")) {
                $address = $request->input("address");
            }
            $advertisement_type = config("constants.advertisement_type");

            $data = new AdvertisementDetail();
            $data->name = $request->input("name");
            $data->link = $request->input("link");
            $data->type = ($address == "" ? (array_search("Regular", $advertisement_type)) : (array_search("Premium", $advertisement_type)));
            $data->status = ($request->input("isSaveDraft") ?? 1);
            $data->publishDate = Carbon::now()->format("Y-m-d H:i:s");
            if ($request->input("expiryDate") != null) {
                $data->expiryDate = Carbon::parse($request->input("expiryDate"))->format("Y-m-d 23:59:59");
            }
            $data->isLocationDetection = $request->input("isLocationDetection") ?? 0;
            $data->address = $address;
            if ($address != "") {
                $url = config("constants.lat_long_url");
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("location" => $address)));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $content  = curl_exec($ch);

                if ($content != "") {
                    $content_array = json_decode($content);
                    if (isset($content_array->results[0]) && isset($content_array->results[0]->locations[0])) {
                        $data->latitude = $content_array->results[0]->locations[0]->latLng->lat;
                        $data->longitude =  $content_array->results[0]->locations[0]->latLng->lng;
                    }
                }
            }
            $data->createdBy = Auth::user()->id;
            $data->updatedBy = Auth::user()->id;
            $data->save();

            $destination_path = public_path("uploads/advertisement");
            if (!File::exists($destination_path)) {
                File::makeDirectory($destination_path);
            }
            foreach ($advertisement_for as $af) {
                $position_array = (($af == "web") ? $web_position : $app_position);
                foreach (count($position_array) > 0 ? $position_array : array() as $wp) {
                    $ad_file_details = new AdvertisementFileDetail();
                    $ad_file_details->advertisementId = $data->id;
                    $ad_file_details->advertisementFor = $af;
                    $ad_file_details->position = $wp;
                    if (isset($image_array[$wp])) {
                        $image_name = $data->id . "_" . $af . "_" . $wp . "." . ($image_array[$wp]->getClientOriginalExtension());
                        $image_array[$wp]->move($destination_path, $image_name);
                        $ad_file_details->imageName = $image_name;
                    }
                    $category_key = $wp;
                    if ($wp == "sidebar_responsive") {
                        $category_key = "sidebar";
                    }
                    $ad_file_details->categoryId = (isset($image_category) ? implode(",", $image_category[$category_key]) : "");
                    $ad_file_details->save();
                }
            }
            return redirect()->route("advertisement-list")->with("success", "Advertisement inserted successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function loadAdvertisementCategory()
    {
        $web_category_data_array = array();
        $web_category_data = CategoryDetail::selectRaw("id,name,parentId,level")->where("name", "!=", "Place An Ad")->where("level", 0)->where('isDisplayInFrontend', 1)->where("isCouponCategory", 0)->orderBy("frontend_menu_index")->get();
        foreach (count((array)$web_category_data) > 0 ? $web_category_data : array() as $c) {
            $web_category_data_array[$c->id] = $c->name;
        }
        $app_category_data_array = array();
        $app_category_data = CategoryDetail::selectRaw("id,name,parentId,level")->where("name", "!=", "Couponzone")->where("level", 0)->where('isDisplayInApp', 1)->where("isCouponCategory", 0)->orderBy("nav_menu_index")->get();
        foreach (count((array)$app_category_data) > 0 ? $app_category_data : array() as $c) {
            $app_category_data_array[$c->id] = $c->name;
        }
        return array("web_category_data_array" => $web_category_data_array, "app_category_data_array" => $app_category_data_array);
    }
    public function editAdvertisement($id)
    {
        try {
            $ad_data = AdvertisementDetail::where("id", $id)->first();
            if (!$ad_data) {
                return view("errors.404");
            }
            $category_array = $this->loadAdvertisementCategory();
            $web_category_data_array = $category_array["web_category_data_array"];
            $app_category_data_array = $category_array["app_category_data_array"];

            $advertisement_data = array();
            $advertisement_data["advertisement_id"] = $ad_data->id;
            $advertisement_data["name"] = $ad_data->name;
            $advertisement_data["link"] = $ad_data->link;
            $advertisement_data["address"] = $ad_data->address;
            if ($ad_data->expiryDate != "") {
                $advertisement_data["expiryDate"] = Carbon::parse($ad_data->expiryDate)->format("Y-m-d");
            }
            $advertisement_data["isLocationDetection"] = $ad_data->isLocationDetection;
            $advertisement_data["advertisementFor"] = array();
            $advertisement_data["web_position"] = array();
            $advertisement_data["app_position"] = array();
            $advertisement_data["position_image"] = array();
            $advertisement_data["image_category"] = array();

            $get_advertisement_files = AdvertisementFileDetail::where("advertisementId", $id)->get();
            foreach (count((array)$get_advertisement_files) > 0 ? $get_advertisement_files : array() as $af) {
                if (!in_array($af->advertisementFor, $advertisement_data["advertisementFor"])) {
                    array_push($advertisement_data["advertisementFor"], $af->advertisementFor);
                }
                if ($af->advertisementFor == "web" && !in_array($af->position, $advertisement_data["web_position"])) {
                    array_push($advertisement_data["web_position"], $af->position);
                }
                if ($af->advertisementFor == "app" && !in_array($af->position, $advertisement_data["app_position"])) {
                    array_push($advertisement_data["app_position"], $af->position);
                }
                $advertisement_data["position_image"][$af->position] = ("../uploads/advertisement/" . $af->imageName);
                if ($af->advertisementFor == "web") {
                    $advertisement_data["image_category"][$af->position] = ($af->categoryId == implode(",", array_keys($web_category_data_array)) ? $af->categoryId : explode(",", $af->categoryId));
                } else {
                    $advertisement_data["image_category"][$af->position] = ($af->categoryId == implode(",", array_keys($app_category_data_array)) ? $af->categoryId : explode(",", $af->categoryId));
                }
            }
            return view("advertisement.add", compact("web_category_data_array", "app_category_data_array", "advertisement_data"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateAdvertisement(Request $request)
    {
        try {
            $advertisement_id = $request->input("advertisement_id");
            if ($advertisement_id == "") {
                return view("errors.403");
            }
            $data = AdvertisementDetail::where("id", $advertisement_id)->first();
            if (!$data) {
                return view("errors.404");
            }
            $positionwise_image = array();
            $get_advertisement_files = AdvertisementFileDetail::where("advertisementId", $advertisement_id)->get();
            foreach (count((array)$get_advertisement_files) > 0 ? $get_advertisement_files : array() as $af) {
                $positionwise_image[$af->position] = $af->imageName;
            }
            $element_array = array(
                'name' => 'required',
                'link' => 'required',
                'advertisementFor' => 'required'
            );
            $advertisement_for = $request->input("advertisementFor");
            $web_position = $request->input("web_position");
            $app_position = $request->input("app_position");
            $image_category = $request->input("image_category");
            $image_array = $request->file("position_image");

            if (isset($advertisement_for)) {
                if (in_array("web", $advertisement_for) && !isset($web_position)) {
                    $element_array['web_position'] = 'required';
                }
                if (in_array("app", $advertisement_for) && !isset($app_position)) {
                    $element_array['app_position'] = 'required';
                }
                foreach ((isset($web_position) && count($web_position) > 0) ? $web_position : array() as $wp) {
                    $category_key = $wp;
                    if($wp == "sidebar_responsive"){
                        $category_key = "sidebar";
                    }
                    if (!isset($image_category) || (isset($image_category) && !isset($image_category[$category_key]))) {
                        $element_array['image_category.' . $category_key] = 'required';
                    }
                    if (!isset($positionwise_image[$wp]) && !isset($image_array[$wp])) {
                        $element_array['position_image.' . $wp] = 'required';
                    }
                }
                foreach ((isset($app_position) && count($app_position) > 0) ? $app_position : array() as $ap) {
                    if (!isset($image_category) || (isset($image_category) && !isset($image_category[$ap]))) {
                        $element_array['image_category.' . $ap] = 'required';
                    }
                    if (!isset($positionwise_image[$ap]) && !isset($image_array[$ap])) {
                        $element_array['position_image.' . $ap] = 'required';
                    }
                }
            }
            if ($request->input("isLocationDetection")) {
                $element_array['address'] = 'required';
            }
            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter advertisement name',
                'link.required' => 'Please enter redirection link',
                'address.required' => 'Please enter address',
                'advertisementFor.required' => 'Please select where advertisement will display',
                'web_position.required' => 'Please select position for web',
                'app_position.required' => 'Please select position for app',
                'image_category.sidebar.required' => 'Please select category',
                'image_category.middle.required' => 'Please select category',
                'image_category.bottom.required' => 'Please select category',
                'image_category.square.required' => 'Please select category',
                'image_category.horizontal.required' => 'Please select category',
                'image_category.tablet_square.required' => 'Please select category',
                'position_image.sidebar.required' => 'Please select image',
                'position_image.sidebar_responsive.required' => 'Please select image',
                'position_image.middle.required' => 'Please select image',
                'position_image.bottom.required' => 'Please select image',
                'position_image.square.required' => 'Please select image',
                'position_image.horizontal.required' => 'Please select image',
                'position_image.tablet_square.required' => 'Please select image'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $address = "";
            if ($request->input("isLocationDetection")) {
                $address = $request->input("address");
            }
            $advertisement_type = config("constants.advertisement_type");

            $data->name = $request->input("name");
            $data->link = $request->input("link");
            $data->type = ($address == "" ? (array_search("Regular", $advertisement_type)) : (array_search("Premium", $advertisement_type)));
            $data->isLocationDetection = $request->input("isLocationDetection") ?? 0;
            if ($request->input("expiryDate") != null) {
                $data->expiryDate = Carbon::parse($request->input("expiryDate"))->format("Y-m-d 23:59:59");
            } else {
                $data->expiryDate = null;
            }
            $data->address = $address;
            if ($address != "") {
                $url = "http://www.mapquestapi.com/geocoding/v1/address?key=McW5scpZZAlY2pfbgWkLuAkCiQTp9DXR";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("location" => $address)));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $content  = curl_exec($ch);

                if ($content != "") {
                    $content_array = json_decode($content);
                    if (isset($content_array->results[0]) && isset($content_array->results[0]->locations[0])) {
                        $data->latitude = $content_array->results[0]->locations[0]->latLng->lat;
                        $data->longitude =  $content_array->results[0]->locations[0]->latLng->lng;
                    }
                }
            } else {
                $data->latitude = "";
                $data->longitude = "";
            }
            $data->updatedBy = Auth::user()->id;
            $data->save();

            $destination_path = public_path("uploads/advertisement");
            if (!File::exists($destination_path)) {
                File::makeDirectory($destination_path);
            }

            AdvertisementFileDetail::where("advertisementId", $data->id)->delete();
            $included_images = array();
            foreach ($advertisement_for as $af) {
                $position_array = (($af == "web") ? $web_position : $app_position);
                foreach (count($position_array) > 0 ? $position_array : array() as $wp) {
                    $ad_file_details = new AdvertisementFileDetail();
                    $ad_file_details->advertisementId = $data->id;
                    $ad_file_details->advertisementFor = $af;
                    $ad_file_details->position = $wp;
                    if (isset($image_array[$wp])) {
                        $image_name = $data->id . "_" . $af . "_" . $wp . "." . ($image_array[$wp]->getClientOriginalExtension());
                        $image_array[$wp]->move($destination_path, $image_name);
                        $ad_file_details->imageName = $image_name;
                        array_push($included_images, $image_name);
                    } else {
                        $ad_file_details->imageName = (isset($positionwise_image[$wp]) ? $positionwise_image[$wp] : "");
                        if ($ad_file_details->imageName != "") {
                            array_push($included_images, $ad_file_details->imageName);
                        }
                    }
                    $category_key = $wp;
                    if ($wp == "sidebar_responsive") {
                        $category_key = "sidebar";
                    }
                    $ad_file_details->categoryId = (isset($image_category) ? implode(",", $image_category[$category_key]) : "");
                    $ad_file_details->save();
                }
            }
            $image_diff = array_diff(array_values($positionwise_image), $included_images);
            foreach (count($image_diff) > 0 ? $image_diff : array() as $i) {
                unlink(public_path("uploads/advertisement/" . $i));
            }
            return redirect()->route("advertisement-list")->with("success", "Advertisement updated successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deleteAdvertisement($advertisement_id)
    {
        try {
            if ($advertisement_id == "") {
                return view("errors.403");
            }
            $file_details = AdvertisementFileDetail::where("advertisementId", $advertisement_id)->get();
            foreach (count((array)$file_details) > 0 ? $file_details : array() as $f) {
                if ($f->imageName != "" && file_exists(public_path("uploads/advertisement/" . $f->imageName))) {
                    unlink(public_path("uploads/advertisement/" . $f->imageName));
                }
                $f->delete();
            }
            AdvertisementDetail::where("id", $advertisement_id)->delete();
            return redirect()->route("advertisement-list")->with("success", "Advertisement deleted successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to delete advertisements
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function deleteAdvertisements()
    {
        try {
            $ids_to_delete = request("ad_ids");
            if ($ids_to_delete == "") {
                return view("errors.403");
            }
            $ad_ids = explode(",", $ids_to_delete);
            foreach (count($ad_ids) > 0 ? $ad_ids : array() as $a) {
                $file_details = AdvertisementFileDetail::where("advertisementId", $a)->get();
                foreach (count((array)$file_details) > 0 ? $file_details : array() as $f) {
                    if ($f->imageName != "" && file_exists(public_path("uploads/advertisement/" . $f->imageName))) {
                        unlink(public_path("uploads/advertisement/" . $f->imageName));
                    }
                    $f->delete();
                }
                AdvertisementDetail::where("id", $a)->delete();
            }
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function publishAdvertisement($advertisement_id)
    {
        try {
            if ($advertisement_id == "") {
                return view("errors.403");
            }
            $data = AdvertisementDetail::where("id", $advertisement_id)->first();
            if ($data) {
                $data->status = array_search("Approved", config('constants.advertisement_status'));
                $data->save();
                return redirect()->route("advertisement-list")->with("success", "Advertisement published successfully");
            }
            abort(403, 'Unauthorized action.');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function unpublishAdvertisement($advertisement_id)
    {
        try {
            if ($advertisement_id == "") {
                return view("errors.403");
            }
            $data = AdvertisementDetail::where("id", $advertisement_id)->first();
            if ($data) {
                $data->status = array_search("Unapproved", config('constants.advertisement_status'));
                $data->save();
                return redirect()->route("advertisement-list")->with("success", "Advertisement unpublished successfully");
            }
            return view("errors.403");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
