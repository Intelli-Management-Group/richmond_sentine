<?php

namespace App\Http\Controllers;


use App\Models\BusinessCategory;
use App\Models\BusinessCategoryDetail;
use App\Models\BusinessDetail;
use App\Models\BusinessPhotosDetail;
use App\Models\BusinessUser;
use App\Models\NotificationDetail;
use App\Models\UserwiseBusinessCategoryDetail;
use App\Models\UserwiseBusinessDetail;
use App\Models\UserwiseBusinessPhotosDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Pusher\Pusher;
use Validator;

class BusinessController extends Controller
{
    /**
     *  Method to load category add form
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function loadCategory()
    {
        try {
            $category_data_array = $this->getCategory();
            return view("business.category", compact("category_data_array"));

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function getCategory()
    {
        $category_data_array = array();
        $category_data = BusinessCategory::selectRaw("id,name,parentId,level")->where("level", 0)->orderBy("id")->get();
        foreach (count((array)$category_data) > 0 ? $category_data : array() as $c) {
            $sub_category_array = array();
            $sub_category_data = BusinessCategory::selectRaw("id,name,parentId,level")->where("level", 1)->where("parentId", $c->id)->orderBy("id")->get();
            foreach (count((array)$sub_category_data) > 0 ? $sub_category_data : array() as $sc) {
                $sub_category_array[$sc->id] = array("name" => $sc->name, "parent_id" => $sc->parentId, "level" => $sc->level, "child_list" => array());
            }
            $category_data_array[$c->id] = array("name" => $c->name, "parent_id" => $c->parentId, "level" => $c->level, "child_list" => $sub_category_array);
        }
        return $category_data_array;
    }

    /**
     *  Method to add category
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function insertCategory(Request $request)
    {
        try {
            $element_array = array(
                'name' => 'required',
            );
            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter category name'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $category_id = request("category_id");

            if ($category_id == null) {
                $data = new BusinessCategory();
            } else {
                $data = BusinessCategory::where("id", $category_id)->first();
            }
            $data->name = $request->input("name");
            $data->parentId = (isset($request->input("parentId")[0]) ? $request->input("parentId")[0] : 0);
            $data->isActualCategory = ($request->input("isActualCategory") ? 1 : 0);
            $data->level = ($data->isActualCategory ? 0 : 1);
            $data->isActive = ($request->input("isActive") ? 1 : 0);
            $data->save();

            return redirect()->route("business-category-list")->with("success", "Category " . ($category_id == null ? "inserted" : "updated") . " successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to edit category
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function editCategory($id)
    {
        try {
            $category_data = BusinessCategory::where("id", $id)->first();
            if ($category_data) {
                $category_data_array = $this->getCategory();
                return view("business.category", compact("category_data", "category_data_array"));
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to delete category
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteCategory($id)
    {
        try {
            $category_data = BusinessCategory::where("id", $id)->first();
            if ($category_data) {
                $category_data->delete();
                return redirect()->route("business-category-list")->with("success", "Category deleted successfully");
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     *  Method to load business list
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function loadBusinessList(Request $request)
    {
        try {
            return view("business.businessList");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load business list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadBusinessListPaginationData(Request $request)
    {
        try {
            $start = \Request::input('start');
            $end = \Request::input('length');
            $search_value = \Request::input('search')['value'];
            $search_value = strtolower($search_value);

            $order_detail = \Request::input('order');
            $sort_order = "DESC";
            $sort_column_name = "created_at";
            $sort_columns = array("1" => "name", "3" => "phone");
            if (count((array)$order_detail) > 0) {
                $sort_column_name = $sort_columns[$order_detail[0]['column']];
                $sort_order = strtoupper($order_detail[0]['dir']);
            }
            $business_data = BusinessDetail::selectRaw("*");
            if ($search_value != "") {
                $business_data = $business_data->whereRaw("(name like '%" . $search_value . "%' OR address1 like '%" . $search_value . "%' OR address2 like '%" . $search_value . "%' OR city like '%" . $search_value . "%' OR province like '%" . $search_value . "%' OR postalCode like '%" . $search_value . "%' OR country like '%" . $search_value . "%' OR email like '%" . $search_value . "%' OR phone like '%" . $search_value . "%' OR website like '%" . $search_value . "%')");
            }
            $total_business = $business_data->count();
            $business_data = $business_data->orderBy($sort_column_name, $sort_order);
            $business_data = $business_data->skip($start)->take($end)->get();

            $business_data_array = array();
            foreach (count((array)$business_data) > 0 ? $business_data : array() as $b) {
                $delete_onclick = url('make-business-featured') . "/" . $b->id;
                $unapprove_onclick = 'javascript:unapproveBusiness(' . $b->id . ');';
                $delete_business = 'javascript:deleteBusiness(' . $b->id . ');';
                array_push($business_data_array, array(
                    "logo" => "<div class='text-center'><img src='" . getBusinessImage($b->logo) . "' class='business_image' alt='" . $b->name . "'/></div>",
                    "name" => $b->name,
                    "address" => getBusinessAddress($b),
                    "phone" => $b->phone,
                    "email" => $b->email,
                    "website" => "<a href='" . $b->website . "' target='_blank'>" . $b->website . "</a>",
                    "action" => '<span class="business-icon"><a href="' . $delete_onclick . '" title="' . ($b->isFeatured ? "Remove featured" : "Mark as featured") . '"><i class="' . ($b->isFeatured ? "fa fa-star fa-lg" : "fa fa-star-o fa-lg") . '" aria-hidden="true"></i></a></span><span class="ml10 business-icon" title="Update business"><a href="' . (url("edit-approved-business") . "/" . $b->id) . '"><i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i></a></span><span class="ml10 business-icon" title="Unapprove business"><a  onclick="' . $unapprove_onclick . '"><i class="fa fa-times fa-lg" aria-hidden="true"></i></a></span><span class="business-delete-icon fa-lg ml10"><a onclick="' . $delete_business . '"><i class="fa fa-trash" aria-hidden="true"></i></a></span>'
                ));
            }
            return json_encode(array(
                "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                "data" => $business_data_array,
                "recordsTotal" => $total_business,
                "recordsFiltered" => $total_business,
            ));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadUnapprovedBusinessList(Request $request)
    {
        try {
            return view("business.unapprovedBusinessList");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load business list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadUnapprovedBusinessListPaginationData(Request $request)
    {
        try {
            $start = \Request::input('start');
            $end = \Request::input('length');
            $search_value = \Request::input('search')['value'];
            $search_value = strtolower($search_value);

            $order_detail = \Request::input('order');
            $sort_order = "DESC";
            $sort_column_name = "created_at";
            $sort_columns = array("2" => "name", "4" => "phone");
            if (count((array)$order_detail) > 0) {
                $sort_column_name = $sort_columns[$order_detail[0]['column']];
                $sort_order = strtoupper($order_detail[0]['dir']);
            }
            $business_data = UserwiseBusinessDetail::selectRaw("*");
            if ($search_value != "") {
                $business_data = $business_data->whereRaw("(name like '%" . $search_value . "%' OR address1 like '%" . $search_value . "%' OR address2 like '%" . $search_value . "%' OR city like '%" . $search_value . "%' OR province like '%" . $search_value . "%' OR postalCode like '%" . $search_value . "%' OR country like '%" . $search_value . "%' OR email like '%" . $search_value . "%' OR phone like '%" . $search_value . "%' OR website like '%" . $search_value . "%')");
            }
            $total_business = $business_data->count();
            $business_data = $business_data->orderBy($sort_column_name, $sort_order);
            $business_data = $business_data->skip($start)->take($end)->get();

            $business_data_array = array();
            foreach (count((array)$business_data) > 0 ? $business_data : array() as $b) {
                $delete_business = 'javascript:deleteBusiness(' . $b->id . ');';
                array_push($business_data_array, array(
                    "logo" => "<div class='text-center'><img src='" . getUnapprovedBusinessImage($b->logo) . "' class='business_image' alt='" . $b->name . "'/></div>",
                    "name" => $b->name,
                    "address" => getBusinessAddress($b),
                    "phone" => $b->phone,
                    "email" => $b->email,
                    "website" => $b->website,
                    "action" => '<span class="business-icon mr10" title="Approve business"><a href="' . (url("approve-business") . "/" . $b->id) . '"><i class="fa fa-check fa-lg" aria-hidden="true"></i></a></span><span class="business-icon mr10" title="Update business"><a href="' . (url("edit-business") . "/" . $b->id) . '"><i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i></a></span><span class="business-delete-icon fa-lg"><a onclick="' . $delete_business . '"><i class="fa fa-trash" aria-hidden="true"></i></a></span><br/>' . ($b->email != null ? '<span class="business-icon" title="Send an email"><a href="mailto:"' . $b->email . '"><i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i></a></span>' : "") . ($b->phone != null ? '<span class="business-icon ml10" title="Make a call">' . '<a href="tel://"' . $b->phone . '"><i class="fa fa-phone fa-lg" aria-hidden="true"></i></a></span>' : ""),
                ));
            }
            return json_encode(array(
                "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                "data" => $business_data_array,
                "recordsTotal" => $total_business,
                "recordsFiltered" => $total_business,
            ));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to edit business
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function editBusiness($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $business_data = UserwiseBusinessDetail::where("id", $id)->first();
            if (!$business_data) {
                return view("errors.404");
            }
            $categories = $this->getCategory();
            $day_array = config("constants.day_name");
            $selected_categories = UserwiseBusinessCategoryDetail::selectRaw("GROUP_CONCAT(categoryId) as cat_ids")->where("businessId", $id)->first();
            if ($selected_categories && $selected_categories->cat_ids != null) {
                $business_data->categoryId = $selected_categories->cat_ids;
            }
            $business_data->businessHours = json_decode($business_data->businessHours);
            $file_data = $business_data->imageDetail;
            $business_data->bemail = $business_data->email;
            unset($business_data->email);
            $action_url = (config("constants.richmond_business_url") . "update-business");
            $base_url = config("constants.richmond_business_url");
            $page_title = array("title" => "Unapproved Business List", "url" => url('unapproved-business-list'));
            return view("business.updateBusiness", compact("categories", "day_array", "business_data", "file_data", "action_url", "base_url", "page_title"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to edit approved business
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function editApprovedBusiness($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $business_data = BusinessDetail::where("id", $id)->first();
            if (!$business_data) {
                return view("errors.404");
            }
            $categories = $this->getCategory();
            $day_array = config("constants.day_name");
            $selected_categories = BusinessCategoryDetail::selectRaw("GROUP_CONCAT(categoryId) as cat_ids")->where("businessId", $id)->first();
            if ($selected_categories && $selected_categories->cat_ids != null) {
                $business_data->categoryId = $selected_categories->cat_ids;
            }
            $business_data->businessHours = json_decode($business_data->businessHours);
            $file_data = $business_data->imageDetail;
            $business_data->bemail = $business_data->email;
            unset($business_data->email);
            $action_url = (url("/") . "/update-business");
            $base_url = url("/") . "/";
            $page_title = array("title" => "Approved Business List", "url" => url('business-list'));
            return view("business.updateBusiness", compact("categories", "day_array", "business_data", "file_data", "action_url", "base_url", "page_title"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to approve business
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveBusiness($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $business_data = UserwiseBusinessDetail::find($id);
            if (!$business_data) {
                return view("errors.404");
            }
            $business = new BusinessDetail();
            $business->name = $business_data->name;
            $business->shortCode = $business_data->shortCode;
            $business->address1 = $business_data->address1;
            $business->address2 = $business_data->address2;
            $business->city = $business_data->city;
            $business->province = $business_data->province;
            $business->postalCode = $business_data->postalCode;
            $business->country = $business_data->country;
            $business->about = $business_data->about;
            $business->email = $business_data->email;
            $business->website = $business_data->website;
            $business->phone = $business_data->phone;
            $business->businessHours = $business_data->businessHours;
            $business->userId = $business_data->createdBy;
            $business->otherCategoryName = $business_data->otherCategoryName;
            $business->save();

            $business_address = getBusinessAddress($business);
            if ($business_address != "") {
                $ll_url = config("constants.lat_long_url");
                $ch = curl_init($ll_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("location" => $business_address)));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $content = curl_exec($ch);
                curl_close($ch);

                if ($content != "") {
                    $content_array = json_decode($content);
                    if (isset($content_array->results[0]) && isset($content_array->results[0]->locations[0])) {
                        $business->latitude = $content_array->results[0]->locations[0]->latLng->lat;
                        $business->longitude = $content_array->results[0]->locations[0]->latLng->lng;
                        $business->save();
                    }
                }
            }

            $category_ids = UserwiseBusinessCategoryDetail::select("categoryId")->where("businessId", $business_data->id)->get();
            foreach (count((array)$category_ids) > 0 ? $category_ids : array() as $c) {
                $business_category = new BusinessCategoryDetail();
                $business_category->businessId = $business->id;
                $business_category->categoryId = $c->categoryId;
                $business_category->save();
            }
            $destination_path = public_path("uploads/business/logo");
            if (!File::exists($destination_path)) {
                File::makeDirectory($destination_path);
            }
            $target_file = config("constants.admin_sub_folder_name") . "/public/uploads/business/logo/" . $business_data->logo;
            $file_info = pathinfo(public_path($target_file));
            File::copy(public_path($target_file), ($destination_path . "/" . $business->id . "." . $file_info['extension']));

            $business->logo = $business->id . "." . $file_info['extension'];
            $business->save();

            $business_folder = "uploads/business/" . $business->id;
            $business_path = public_path($business_folder);
            if (!File::exists($business_path)) {
                File::makeDirectory($business_path);
            }
            foreach (count((array)$business_data->imageDetail) > 0 ? $business_data->imageDetail : array() as $i) {
                $business_image = new BusinessPhotosDetail();
                $business_image->businessId = $business->id;
                $business_image->type = $i->type;
                $business_image->originalName = $i->originalName;
                $business_image->save();

                $b_target_file = config("constants.admin_sub_folder_name") . "/public/uploads/business/" . $business_data->id . "/" . $i->fileName;
                $business_file_info = pathinfo(public_path($b_target_file));
                File::copy(public_path($b_target_file), ($business_path . "/" . ($business_image->id . "." . $business_file_info['extension'])));
                $business_image->fileName = $business_image->id . "." . $business_file_info['extension'];
                $business_image->save();
            }
            foreach (count((array)$business_data->imageDetail) > 0 ? $business_data->imageDetail : array() as $f) {
                if ($f->fileName != null && file_exists(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/" . $f->businessId . "/" . $f->fileName))) {
                    unlink(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/" . $f->businessId . "/" . $f->fileName));
                }
                $f->delete();
            }
            UserwiseBusinessCategoryDetail::where("businessId", $business_data->id)->delete();
            if ($business_data->logo != null && file_exists(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/logo/" . $business_data->logo))) {
                unlink(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/logo/" . $business_data->logo));
            }
            NotificationDetail::where("userBusinessId", $business_data->id)->delete();
            File::deleteDirectory(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/" . $business_data->id));
            $business_data->delete();
            return redirect()->route("unapproved-business-list")->with("success", "Business approved successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to make business featured
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleBusinessFeatured($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $business_data = BusinessDetail::find($id);
            if (!$business_data) {
                return view("errors.404");
            }
            $business_data->isFeatured = !$business_data->isFeatured;
            $business_data->save();
            return redirect()->route("business-list")->with("success", ($business_data->isFeatured ? "Business featured successfully" : "Business removed from featured"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateBusiness(Request $request)
    {
        try {
            $current_step = intval($request->input("current_step"));
            $element_array = $data = $dayTimeArray = array();
            $allMediaFiles = $request->file("allMediaFiles");
            $business_id = $request->input("business_id");
            $deleted_ids = $request->input("deleted_ids");
            $is_open_value = $request->input("isOpen");
            $start_time = $request->input("starttime");
            $end_time = $request->input("endtime");
            $url = (isset(Auth::user()->user_name) ? (url("/") . "/") : config("constants.richmond_business_url"));

            if ($business_id == null) {
                return response()->json(array("list" => $url . "business-list", "msg" => "No business found", "type" => "error"));
            } else {
                $data = BusinessDetail::where("id", $business_id)->first();
                if (!$data) {
                    return response()->json(array("list" => $url . "business-list", "msg" => "No business found", "type" => "error"));
                }
                if (isset($data->userId)) {
                    $business_user = BusinessUser::where("id", $data->userId)->first();
                    if (!$business_user) {
                        return response()->json(array("list" => url("/logout"), "msg" => "User not found", "type" => "error"));
                    }
                }
            }
            if ($current_step == 0) {
                $element_array = array(
                    'name' => 'required',
                    'about' => 'required',
                    'shortCode' => 'required|unique:business_details,shortCode,' . $data->id . ',id,deleted_at,NULL|unique:userwise_business_details,shortCode,NULL,id,deleted_at,NULL'
                );
                if ($request->input('business_category') == "other" && $request->input('otherCategoryName') == null) {
                    $element_array["otherCategoryName"] = 'required';
                } else if ($request->input('business_category') != "other") {
                    $element_array["categoryId"] = 'required';
                }
            } else if ($current_step == 1) {
                $element_array = array(
                    'address1' => 'required',
                    'city' => 'required',
                    'province' => 'required',
                    'postalCode' => 'required',
                    'country' => 'required'
                );
            } else if ($current_step == 2) {
                if ($data->logo == null) {
                    $element_array = array(
                        'logo' => 'required'
                    );
                }
            } else if ($current_step == 3) {
                $day_names = config("constants.day_name");
                if (!isset($start_time) && !isset($end_time)) {
                    $element_array['businesstime'] = 'required';
                } else {
                    $is_fail = 0;
                    foreach ($day_names as $day => $value) {
                        if (!isset($dayTimeArray[$day])) {
                            $dayTimeArray[$day] = array();
                        }
                        if (isset($is_open_value[$day])) {
                            foreach ($start_time[$day] as $index => $time) {
                                $ending_time = $end_time[$day][$index];
                                if (empty($time) || empty($ending_time)) {
                                    $is_fail++;
                                } else if (Carbon::parse($time)->gt(Carbon::parse($ending_time))) {
                                    $is_fail++;
                                } else {
                                    array_push($dayTimeArray[$day], array("start_time" => $time, "end_time" => $end_time[$day][$index]));
                                }
                            }
                        }
                    }
                    if ($is_fail > 0)
                        $element_array['businesstime'] = 'required';
                }
            }
            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter name',
                'shortCode.required' => 'Please enter short code',
                'logo.required' => 'Please select logo image',
                'address1.required' => 'Please enter address',
                'city.required' => 'Please enter city',
                'province.required' => 'Please enter province',
                'postalCode.required' => 'Please enter postal code',
                'country.required' => 'Please enter country',
                'categoryId.required' => 'Please select category',
                'about.required' => 'Please enter about',
                'businesstime.required' => 'Please enter valid opening hours',
                'otherCategoryName.required' => 'Please enter category name'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
            if ($current_step == 0) {
                $data->name = $request->input("name");
                $data->shortCode = $request->input("shortCode");
                if ($request->input("business_category")) {
                    if ($business_id != null) {
                        BusinessCategoryDetail::where("businessId", $business_id)->delete();
                    }
                    if ($request->input('business_category') == "other") {
                        $data->otherCategoryName = $request->input("otherCategoryName");
                    } else {
                        $category_id = $request->input("categoryId");
                        $data->otherCategoryName = null;

                        $business_category = new BusinessCategoryDetail();
                        $business_category->businessId = $data->id;
                        $business_category->categoryId = $category_id;
                        $business_category->save();
                    }
                }
                $data->about = $request->input("about");
                $data->save();
            } else if ($current_step == 1) {
                $data->address1 = $request->input("address1");
                $data->address2 = $request->input("address2");
                $data->city = $request->input("city");
                $data->province = $request->input("province");
                $data->postalCode = $request->input("postalCode");
                $data->country = $request->input("country");
                $data->website = $request->input("website");
                $data->phone = $request->input("phone");
                $data->save();

                $business_address = getBusinessAddress($data);
                if ($business_address != "") {
                    $ll_url = config("constants.lat_long_url");
                    $ch = curl_init($ll_url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("location" => $business_address)));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $content = curl_exec($ch);
                    curl_close($ch);

                    if ($content != "") {
                        $content_array = json_decode($content);
                        if (isset($content_array->results[0]) && isset($content_array->results[0]->locations[0])) {
                            $data->latitude = $content_array->results[0]->locations[0]->latLng->lat;
                            $data->longitude = $content_array->results[0]->locations[0]->latLng->lng;
                            $data->save();
                        }
                    }
                }
            } else if ($current_step == 2) {
                if ($request->file("logo")) {
                    $image = $request->file("logo");
                    $image_name = $data->id . "." . $image->clientExtension();
                    $destination_path = public_path("uploads/business/logo");

                    if (!File::exists($destination_path)) {
                        File::makeDirectory($destination_path);
                    }
                    $image->move($destination_path, $image_name);
                    $data->logo = $image_name;
                    $data->save();
                }
                $business_folder = "uploads/business/" . $data->id;
                $destination_path = public_path($business_folder);
                if (!File::exists($destination_path)) {
                    File::makeDirectory($destination_path);
                }
                if ($deleted_ids != null) {
                    $deleted_ids_array = explode(",", $deleted_ids);
                    foreach (count($deleted_ids_array) > 0 ? $deleted_ids_array : array() as $d) {
                        $file = BusinessPhotosDetail::where("id", $d)->where("businessId", $data->id)->first();
                        if ($file) {
                            if ($file->fileName != null && file_exists(public_path($business_folder . "/" . $file->fileName))) {
                                unlink(public_path($business_folder . "/" . $file->fileName));
                            }
                            $file->delete();
                        }
                    }
                }
                foreach ((!empty($allMediaFiles) && count($allMediaFiles) > 0) ? $allMediaFiles : array() as $key => $value) {
                    $business_file = new BusinessPhotosDetail();
                    $business_file->businessId = $data->id;
                    $business_file->type = "image";
                    $file_name = $value->getClientOriginalName();
                    $business_file->originalName = $file_name;
                    $business_file->save();

                    $new_file_name = $business_file->id . "." . $value->clientExtension();
                    $value->move($destination_path, $new_file_name);
                    $business_file->fileName = $new_file_name;
                    $business_file->save();
                }
            } else if ($current_step == 3) {
                $data->businessHours = json_encode($dayTimeArray);
                $data->save();
            }
            if (!isset(Auth::user()->user_name)) {
                $message = "{user_name} has updated business details";
                $notification = new NotificationDetail();
                $notification->notificationType = "update-business";
                $notification->notificationText = $message;
                $notification->businessId = $data->id;
                $notification->sentTime = Carbon::now()->format("Y-m-d h:i:s");
                $notification->businessUserId = $data->userId;
                $notification->save();

                $options = array(
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'encrypted' => true
                );
                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    $options
                );
                $pusher->trigger('business', 'update-business', $message);
            }
            $msg = "Business updated successfully";
            return response()->json(array("list" => $url . "business-list", "msg" => $msg, "type" => "success"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to unapprove business
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unapproveBusiness(Request $request)
    {
        try {
            $business_id = $request->input("business_id");
            $business_data = BusinessDetail::find($business_id);
            if (!$business_data) {
                return "error";
            }
            $business = new UserwiseBusinessDetail();
            $business->name = $business_data->name;
            $business->shortCode = $business_data->shortCode;
            $business->address1 = $business_data->address1;
            $business->address2 = $business_data->address2;
            $business->city = $business_data->city;
            $business->province = $business_data->province;
            $business->postalCode = $business_data->postalCode;
            $business->country = $business_data->country;
            $business->about = $business_data->about;
            $business->email = $business_data->email;
            $business->website = $business_data->website;
            $business->phone = $business_data->phone;
            $business->businessHours = $business_data->businessHours;
            $business->otherCategoryName = $business_data->otherCategoryName;
            $business->createdBy = $business_data->userId;
            $business->save();

            $category_ids = BusinessCategoryDetail::select("categoryId")->where("businessId", $business_data->id)->get();
            foreach (count((array)$category_ids) > 0 ? $category_ids : array() as $c) {
                $business_category = new UserwiseBusinessCategoryDetail();
                $business_category->businessId = $business->id;
                $business_category->categoryId = $c->categoryId;
                $business_category->save();
            }
            $destination_path = public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/logo");
            if (!File::exists($destination_path)) {
                File::makeDirectory($destination_path);
            }
            $target_file = "/uploads/business/logo/" . $business_data->logo;
            $file_info = pathinfo(public_path($target_file));
            File::copy(public_path($target_file), ($destination_path . "/" . $business->id . "." . $file_info['extension']));

            $business->logo = $business->id . "." . $file_info['extension'];
            $business->save();

            $business_folder = config("constants.admin_sub_folder_name") . "/public/uploads/business/" . $business->id;
            $business_path = public_path($business_folder);
            if (!File::exists($business_path)) {
                File::makeDirectory($business_path);
            }
            foreach (count((array)$business_data->imageDetail) > 0 ? $business_data->imageDetail : array() as $i) {
                $business_image = new UserwiseBusinessPhotosDetail();
                $business_image->businessId = $business->id;
                $business_image->type = $i->type;
                $business_image->originalName = $i->originalName;
                $business_image->save();

                $b_target_file = "/uploads/business/" . $business_data->id . "/" . $i->fileName;
                $business_file_info = pathinfo(public_path($b_target_file));
                File::copy(public_path($b_target_file), ($business_path . "/" . ($business_image->id . "." . $business_file_info['extension'])));
                $business_image->fileName = $business_image->id . "." . $business_file_info['extension'];
                $business_image->save();
            }
            foreach (count((array)$business_data->imageDetail) > 0 ? $business_data->imageDetail : array() as $f) {
                if ($f->fileName != null && file_exists(public_path("/uploads/business/" . $f->businessId . "/" . $f->fileName))) {
                    unlink(public_path("/uploads/business/" . $f->businessId . "/" . $f->fileName));
                }
                $f->delete();
            }
            BusinessCategoryDetail::where("businessId", $business_data->id)->delete();
            if ($business_data->logo != null && file_exists(public_path("/uploads/business/logo/" . $business_data->logo))) {
                unlink(public_path("/uploads/business/logo/" . $business_data->logo));
            }
            NotificationDetail::where("businessId", $business_data->id)->delete();
            File::deleteDirectory(public_path("/uploads/business/" . $business_data->id));
            $business_data->delete();
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Method to delete approved business
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteApprovedBusiness(Request $request)
    {
        try {
            $id_to_delete = $request->input("business_id");
            if ($id_to_delete == "") {
                return "error";
            }
            $business_data = BusinessDetail::find($id_to_delete);
            if (!$business_data) {
                return "error";
            }
            foreach (count((array)$business_data->imageDetail) > 0 ? $business_data->imageDetail : array() as $f) {
                if ($f->fileName != null && file_exists(public_path("uploads/business/" . $f->businessId . "/" . $f->fileName))) {
                    unlink(public_path("uploads/business/" . $f->businessId . "/" . $f->fileName));
                }
                $f->delete();
            }
            BusinessCategoryDetail::where("businessId", $business_data->id)->delete();
            if ($business_data->logo != null && file_exists(public_path("uploads/business/logo/" . $business_data->logo))) {
                unlink(public_path("uploads/business/logo/" . $business_data->logo));
            }
            NotificationDetail::where("businessId", $business_data->id)->delete();
            File::deleteDirectory(public_path("uploads/business/" . $business_data->id));
            if (isset($business_data->userDetail)) {
                BusinessUser::where("id", $business_data->userId)->delete();
            }
            $business_data->delete();
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Method to delete unapproved business
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUnapprovedBusiness(Request $request)
    {
        try {
            $id_to_delete = $request->input("business_id");
            if ($id_to_delete == "") {
                return "error";
            }
            $business_data = UserwiseBusinessDetail::find($id_to_delete);
            if (!$business_data) {
                return "error";
            }
            foreach (count((array)$business_data->imageDetail) > 0 ? $business_data->imageDetail : array() as $f) {
                if ($f->fileName != null && file_exists(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/" . $f->businessId . "/" . $f->fileName))) {
                    unlink(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/" . $f->businessId . "/" . $f->fileName));
                }
                $f->delete();
            }
            UserwiseBusinessCategoryDetail::where("businessId", $business_data->id)->delete();
            if ($business_data->logo != null && file_exists(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/logo/" . $business_data->logo))) {
                unlink(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/logo/" . $business_data->logo));
            }
            NotificationDetail::where("userBusinessId", $business_data->id)->delete();
            File::deleteDirectory(public_path(config("constants.admin_sub_folder_name") . "/public/uploads/business/" . $business_data->id));
            if (isset($business_data->userDetail)) {
                BusinessUser::where("id", $business_data->createdBy)->delete();
            }
            $business_data->delete();
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadNotificationList()
    {
        return view("common.notificationList");
    }

    /**
     * Method to mark notification as read
     */

    public function readNotification(Request $request)
    {
        try {
            $id = $request->input('id');
            $notification = NotificationDetail:: where('id', $id)->first();
            if ($notification) {
                $notification->isRead = 1;
                $notification->save();
                return "success";
            }
            return "error";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
