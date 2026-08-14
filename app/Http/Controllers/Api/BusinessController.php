<?php

namespace App\Http\Controllers\Api;

use App\Models\BusinessCategory;
use App\Models\BusinessCategoryDetail;
use App\Models\BusinessDetail;
use App\Models\EventDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class BusinessController extends Controller
{

    /**
     *  Method to get business data
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getBusinessCategoryData()
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $all_categories = BusinessCategory::where("isActive", 1)->orderBy("name", "asc")->get();
            $categories = $alphabetical_categories = $featured = array();
            foreach (count((array)$all_categories) > 0 ? $all_categories : array() as $c) {
                $business_details = BusinessCategoryDetail::where("categoryId", $c->id)->get();
                $businesses = array();
                foreach (count((array)$business_details) > 0 ? $business_details : array() as $b) {
                    if (isset($b->businessDetail)) {
                        $business_data = $b->businessDetail;
                        $first_letter = strtoupper(substr($business_data->name, 0, 1));
                        if (!isset($alphabetical_categories[$first_letter]))
                            $alphabetical_categories[$first_letter] = array("name" => $first_letter, "childs" => array());

                        $data = array("id" => $business_data->id, "name" => $business_data->name, "phone" => $business_data->phone, "address" => getBusinessAddress($business_data), "logo" => getBusinessImage($business_data->logo), "email" => $business_data->email, "website" => $business_data->website, "latitude" => $business_data->latitude, "longitude" => $business_data->longitude);
                        array_push($alphabetical_categories[$first_letter]["childs"], $data);
                        array_push($businesses, $data);

                    }
                }
                array_push($categories, array("id" => $c->id, "name" => $c->name, "childs" => $businesses));
            }
            $featured_business = BusinessDetail::where("isFeatured", 1)->get();
            foreach (count((array)$featured_business) > 0 ? $featured_business : array() as $f) {
                array_push($featured, array("id" => $f->id, "name" => $f->name, "phone" => $f->phone, "address" => getBusinessAddress($f), "logo" => getBusinessImage($f->logo), "email" => $f->email, "website" => $f->website, "latitude" => $f->latitude, "longitude" => $f->longitude));
            }
            return $this->sendResultJSON("1", "", array("featured" => $featured, "category" => $categories, "alphabetical_category" => array_values($alphabetical_categories)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get all business list
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getBusinessList()
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $data = array();
            $all_business = BusinessDetail::all();
            foreach (count((array)$all_business) > 0 ? $all_business : array() as $b) {
                array_push($data, array("id" => $b->id, "logo" => getBusinessImage($b->logo), "name" => $b->name, "address" => getBusinessAddress($b)));
            }
            return $this->sendResultJSON("1", "", array("search_result" => $data));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get all event list
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getEventList(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $data = array();
            $search_date = $request->input("event_date") ?? Carbon::now()->format("Y-m-d");
            $events = EventDetail::where("eventDate", $search_date)->get();
            foreach (count((array)$events) > 0 ? $events : array() as $e) {
                array_push($data, getEventData($e));
            }
            return $this->sendResultJSON("1", "", array("events" => $data));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to get event list from date
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getEventListFromDate(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $data = array();
            $search_date = $request->input("event_date") ?? Carbon::now()->format("Y-m");
            $start_date = $request->input("start_date") ?? Carbon::now()->format("Y-m-d");
            $events = EventDetail::whereRaw('DATE_FORMAT(`eventDate`, "%Y-%m") = "' . $search_date . '" AND eventDate >= "' . $start_date . '"')->orderBy("eventDate", "asc")->orderBy("eventTime", "asc")->get();
            foreach (count((array)$events) > 0 ? $events : array() as $e) {
                if (!isset($data[$e->eventDate])) {
                    $data[$e->eventDate] = array("date" => $e->eventDate, "data" => array());
                }
                array_push($data[$e->eventDate]["data"], getEventData($e));
            }
            return $this->sendResultJSON("1", "", array("events" => array_values($data)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }


}
