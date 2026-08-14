<?php

namespace App\Http\Controllers;

use App\Models\BusinessDetail;
use App\Models\CategoryDetail;
use App\Models\EventCategory;
use App\Models\EventDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;

class EventController extends Controller
{
    /**
     *  Method to load category add form
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function loadCategory()
    {
        try {
            $category_data_array = EventCategory::all();
            return view("event.category", compact("category_data_array"));

        } catch (\Exception $e) {
            return $e->getMessage();
        }
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
                $data = new EventCategory();
            } else {
                $data = EventCategory::where("id", $category_id)->first();
            }
            $data->name = $request->input("name");
            $data->isActive = ($request->input("isActive") ? 1 : 0);
            $data->save();

            return redirect()->route("event-category-list")->with("success", "Category " . ($category_id == null ? "inserted" : "updated") . " successfully");
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
            $category_data = EventCategory::where("id", $id)->first();
            $category_data_array = EventCategory::all();
            if ($category_data) {
                return view("event.category", compact("category_data", "category_data_array"));
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
            $category_data = EventCategory::where("id", $id)->first();
            if ($category_data) {
                $category_data->delete();
                return redirect()->route("event-category-list")->with("success", "Category deleted successfully");
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     *  Method to load event list
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function loadEventList(Request $request)
    {
        try {
            return view("event.eventList");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load event list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadEventPaginationData(Request $request)
    {
        try {
            $start = \Request::input('start');
            $end = \Request::input('length');
            $search_value = \Request::input('search')['value'];
            $search_value = strtolower($search_value);

            $order_detail = \Request::input('order');
            $sort_order = "DESC";
            $sort_column_name = "created_at";
            $sort_columns = array("1" => "name", "2" => "eventDate");
            if (count((array)$order_detail) > 0) {
                $sort_column_name = $sort_columns[$order_detail[0]['column']];
                $sort_order = strtoupper($order_detail[0]['dir']);
            }
            $event_data = EventDetail::selectRaw("id,name,description,eventDate,eventTime,venue,venueAddress,categoryId,bannerImage");
            if ($search_value != "") {
                $event_data = $event_data->whereRaw("(name like '%" . $search_value . "%' OR description like '%" . $search_value . "%' OR eventDate like '%" . $search_value . "%' OR venue like '%" . $search_value . "%' OR venueAddress like '%" . $search_value . "%')");
            }
            $total_events = $event_data->count();
            $event_data = $event_data->orderBy($sort_column_name, $sort_order);
            $event_data = $event_data->skip($start)->take($end)->get();

            $event_data_array = array();
            foreach (count((array)$event_data) > 0 ? $event_data : array() as $e) {
                $delete_onclick = 'javascript:deleteEvent(' . $e->id . ');';
                array_push($event_data_array, array(
                    "checkbox" => "<input type='checkbox' value='" . $e->id . "' class='delete_entity'/>",
                    "name" => $e->name,
                    "date" => $e->eventDate . "<br/>" . $e->eventTime,
                    "category" => $e->eventCategory->name,
                    "venue" => $e->venue,
                    "bannerImage" => "<div class='text-center'><img src='" . getEventBanner($e->bannerImage) . "' class='event_image' alt='" . $e->name . "'/></div>",
                    "action" => '<span class="editicn"><a href="' . (url("edit-event") . "/" . $e->id) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span><span class="deleteicn"><a onclick="' . $delete_onclick . '"><i class="fa fa-trash" aria-hidden="true"></i></a></span>',
                ));
            }
            return json_encode(array(
                "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                "data" => $event_data_array,
                "recordsTotal" => $total_events,
                "recordsFiltered" => $total_events,
            ));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to load add event view
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function loadAddEvent()
    {
        try {
            $all_business = $this->getBusinessData();
            $event_categories = $this->getEventCategories();
            return view("event.addEvent", compact("all_business", "event_categories"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * Method to insert event data
     */
    public function insertEvent(Request $request)
    {
        try {
            $element_array = array(
                'name' => 'required',
                'description' => 'required',
                'eventDate' => 'required',
                'eventTime' => 'required',
                'venue' => 'required',
                'venueAddress' => 'required',
                'organizerName' => 'required',
                'categoryId' => 'required',
                'price' => 'required',
            );
            $event_id = $request->input("event_id");
            if ($event_id == null) {
                $data = new EventDetail();
                $element_array['bannerImage'] = 'required';
            } else {
                $data = EventDetail::find($event_id);
            }
            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter name',
                'description.required' => 'Please enter description',
                'eventDate.required' => 'Please enter event date',
                'eventTime.required' => 'Please enter event time',
                'venue.required' => 'Please enter venue',
                'venueAddress.required' => 'Please enter venue address',
                'organizerName.required' => 'Please enter organizer name',
                'categoryId.required' => 'Please enter category',
                'price.required' => 'Please select price',
                'bannerImage.required' => 'Please select banner image'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
            $data->name = $request->input("name");
            $data->description = $request->input("description");
            $data->eventDate = $request->input("eventDate");
            $data->eventTime = $request->input("eventTime");
            $data->venue = $request->input("venue");
            $data->venueAddress = $request->input("venueAddress");
            $data->venuePhone = $request->input("venuePhone");
            $data->organizerName = $request->input("organizerName");
            $data->organizerPhone = $request->input("organizerPhone");
            $data->organizerEmail = $request->input("organizerEmail");
            $data->organizerWebsite = $request->input("organizerWebsite");
            $data->bookingLink = $request->input("bookingLink");
            $data->linkText = $request->input("linkText");
            $data->businessId = $request->input("businessId");
            $data->categoryId = $request->input("categoryId");
            $selected_price = $request->input("price");
            $data->price = json_encode(array("option" => $selected_price, "value" => ($selected_price == "price" ? $request->input("cost") : "")));
            $data->save();

            if ($request->file("bannerImage")) {
                $image = $request->file("bannerImage");
                $image_name = $data->id . "." . $image->clientExtension();
                $destination_path = public_path("uploads/event/");

                if (!File::exists($destination_path)) {
                    File::makeDirectory($destination_path);
                }
                $image->move($destination_path, $image_name);
                $data->bannerImage = $image_name;
                $data->save();
            }
            $msg = ("Event " . ($event_id == null ? "inserted" : "updated") . " successfully");
            \Request::session()->flash('success', $msg);
            return response()->json(array("list" => url("event-list")));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Method to edit event data
     */
    public function editEvent($id)
    {
        if ($id == "") {
            return view("errors.403");
        }
        $event_data = EventDetail::where("id", $id)->first();
        if (!$event_data) {
            return view("errors.404");
        }
        $all_business = $this->getBusinessData();
        $event_categories = $this->getEventCategories();
        $price_value = json_decode($event_data->price);
        $event_data->price = $price_value->option;
        $event_data->cost = $price_value->value;
        return view("event.addEvent", compact("all_business", "event_categories", "event_data"));
    }

    /*
     * Method to get all business
     */

    private function getBusinessData()
    {
        $business_array = array();
        $all_business = BusinessDetail::selectRaw("id,name")->get();
        foreach (count((array)$all_business) > 0 ? $all_business : array() as $b) {
            $business_array[$b->id] = $b->name;
        }
        return $business_array;
    }

    /*
     * Method to get all event categories
     */
    private function getEventCategories()
    {
        $event_category = array();
        $all_category = EventCategory::selectRaw("id,name")->get();
        foreach (count((array)$all_category) > 0 ? $all_category : array() as $c) {
            $event_category[$c->id] = $c->name;
        }
        return $event_category;
    }

    /**
     * Method to delete events
     */
    public function deleteEvent(Request $request)
    {
        try {
            $ids_to_delete = $request->input("event_ids");
            if ($ids_to_delete == "") {
                return view("errors.403");
            }
            $event_ids = explode(",", $ids_to_delete);
            foreach (count((array)$event_ids) > 0 ? $event_ids : array() as $e) {
                $event_data = EventDetail::find($e);
                if ($event_data) {
                    if ($event_data->bannerImage != null && file_exists(public_path("uploads/event/" . $event_data->bannerImage))) {
                        unlink(public_path("uploads/event/" . $event_data->bannerImage));
                    }
                    $event_data->delete();
                }
            }
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
