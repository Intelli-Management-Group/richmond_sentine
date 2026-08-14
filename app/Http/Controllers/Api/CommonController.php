<?php

namespace App\Http\Controllers\Api;

use App\Models\FooterContentDetail;
use App\Models\OurTeamDetail;
use App\Models\ContactUsDetail;
use App\Models\StepsDetail;
use App\Models\UserwiseStepsDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CommonController extends Controller
{
    /**
     *  Method to get category wise article data
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getFooterContent()
    {
        try {
            $request_type = request("request_type");
            if ($request_type == "") {
                return $this->sendResultJSON("0", "Please enter content type");
            }
            $content_data = array();
            if (in_array($request_type, array("about_us", "terms_of_use", "privacy_policy"))) {
                $data = FooterContentDetail::where("type", $request_type)->first();
                if ($data) {
                    $content_array = json_decode($data->content);
                    // $content_array->description = "<style type='text/css'>p,ul{color: #333333;font: 16.0px 'Helvetica';text-align:justify}h3{text-transform: uppercase;color: #3a53a0;font: 18.0px 'Helvetica';font-weight: bold;margin-bottom:8px;}</style>" . $content_array->description;
                    array_push($content_data, $content_array);
                }
            } else if ($request_type == "our_team") {
                $team_data = OurTeamDetail::selectRaw("name,profile_image,position,twitter_handle,email_id,description")->get();
                foreach (count((array)$team_data) > 0 ? $team_data : array() as $t) {
                    array_push($content_data, $t);
                }
            } else if ($request_type == "contact_us") {
                $contact_us_data = ContactUsDetail::selectRaw("email_id,about_us_email,phone,place_an_ad_phone,office_days,office_hours,mailing_address")->first();
                if ($contact_us_data) {
                    $content_data = $contact_us_data;
                    $content_data["place_an_ad_phone"] = explode(",", $content_data->place_an_ad_phone);
                    $content_data["place_an_ad_image"] = asset("uploads/advertisement/place_an_ad.jpg");
                    $content_data["google_map_url"] = "https://www.google.com/maps/d/u/0/embed?mid=1D2Xlmy-e_rmvtnVQEgjdwaceA_-tUGP0";
                }
            }
            return $this->sendResultJSON("1", "", array("content" => $content_data));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to update user steps
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserSteps(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            if (!session("user_details")->isVerified) {
                return $this->sendResultJSON("0", "User not verified");
            }
            $user_id = session("user_details")->id;
            $steps_data = UserwiseStepsDetail::where("userId", $user_id)->first();
            if (!$steps_data) {
                $steps_data = new UserwiseStepsDetail();
                $steps_data->userId = $user_id;
            }
            if ($request->input("totalSteps")) {
                $steps_data->totalSteps = intval($request->input("totalSteps"));
                $steps_data->save();
            }

            if ($request->input("todaySteps")) {
                $timezone = $request->input("timezone");
                $step_date = Carbon::parse($request->input("stepDateTime"), $timezone);
                $formated_date = $step_date->format("Y-m-d");
                $time_slot_array = array("12am-6am" => array("start_time" => Carbon::createFromFormat('Y-m-d H:i a', ($formated_date . " 00:00 AM"), $timezone), "end_time" => Carbon::createFromFormat('Y-m-d H:i a', ($formated_date . " 05:59 AM"), $timezone)), "6am-12pm" => array("start_time" => Carbon::createFromFormat('Y-m-d H:i a', ($formated_date . " 06:00 AM"), $timezone), "end_time" => Carbon::createFromFormat('Y-m-d H:i a', ($formated_date . " 11:59 AM"), $timezone)), "12pm-6pm" => array("start_time" => Carbon::createFromFormat('Y-m-d H:i a', ($formated_date . " 12:00 PM"), $timezone), "end_time" => Carbon::createFromFormat('Y-m-d H:i a', ($formated_date . " 05:59 PM"), $timezone)), "6pm-12am" => array("start_time" => Carbon::createFromFormat('Y-m-d H:i a', ($formated_date . " 06:00 PM"), $timezone), "end_time" => Carbon::createFromFormat('Y-m-d H:i a', ($formated_date . " 11:59 PM"), $timezone)));
                $todays_data = StepsDetail::where("userId", $user_id)->where("stepDate", $formated_date)->first();
                if (!$todays_data) {
                    $todays_data = new StepsDetail();
                    $todays_data->userId = $user_id;
                    $todays_data->stepDate = $formated_date;
                    $time_data = array();
                    foreach ($time_slot_array as $key => $value) {
                        $time_data[$key] = 0;
                    }
                    $todays_data->hourwiseSteps = json_encode($time_data);
                }
                $todays_data->totalSteps = intval($request->input("todaySteps"));
                $todays_data->save();
                $timeslot_data = json_decode($todays_data->hourwiseSteps, true);
                foreach ($time_slot_array as $key => $value) {
                    if ($step_date->between($value["start_time"], $value["end_time"], true)) {
                        $steps_counter = 0;
                        foreach (count($timeslot_data) > 0 ? $timeslot_data : array() as $ts => $ts_value) {
                            if ($ts == $key) {
                                $timeslot_data[$ts] = intval($request->input("todaySteps")) - $steps_counter;
                            }
                            $steps_counter = $steps_counter + $ts_value;
                        }
                    }
                }
                $todays_data->hourwiseSteps = json_encode($timeslot_data);
                $todays_data->save();
            }

            return $this->sendResultJSON("1", "Steps updated successfully.");
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    /**
     *  Method to top users of step up app
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stepsLeaderboards(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $all_users_data = UserwiseStepsDetail::orderBy("totalSteps", "desc")->take(10)->get();
            $data = array();
            $rank = $user_rank = 1;
            $is_user_found = 0;
            foreach (!$all_users_data->isEmpty() ? $all_users_data : array() as $u) {
                if ($u->userDetail) {
                    $user_data = $u->userDetail;
                    if ($u->userId == $user_id) {
                        $user_rank = $rank;
                        $is_user_found = 1;
                    }
                    array_push($data, array("id" => $user_data->id, "rank" => $rank, "avatar" => getUserAvatarForLeaderboard($user_data->avatar), "name" => $user_data->name, "totalSteps" => ($u->totalSteps != null ? $u->totalSteps : "0")));
                    $rank++;
                }
            }
            if (!$is_user_found) {
                $l_user_data = session("user_details");
                $all_users = UserwiseStepsDetail::orderBy("totalSteps", "desc")->get();
                $rank = 1;
                foreach (!$all_users->isEmpty() ? $all_users : array() as $u) {
                    if ($u->userId == $user_id) {
                        $user_rank = $rank;
                        break;
                    }
                    $rank++;
                }
                array_push($data, array("id" => $l_user_data->id, "rank" => $user_rank, "avatar" => getUserAvatarForLeaderboard($l_user_data->avatar), "name" => $l_user_data->name, "totalSteps" => ($l_user_data->stepDetail ? $l_user_data->stepDetail->totalSteps : "0")));
            }
            return $this->sendResultJSON("1", "", array("all_users" => $data, "user_rank" => $user_rank));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }

    public function getStatistics(Request $request)
    {
        try {
            if (!session("user_details")) {
                return $this->sendResultJSON("11", "Unauthorised");
            }
            $user_id = session("user_details")->id;
            $duration = $request->input("duration");
            $result = array();
            if ($duration == "month" || $duration == "week") {
                $start_date = $request->input("start_date");
                $end_date = $request->input("end_date");
                $parsed_start_date = Carbon::createFromFormat("Y-m-d", $start_date);
                $parsed_end_date = Carbon::createFromFormat("Y-m-d", $end_date);
                while ($parsed_start_date->lte($parsed_end_date)) {
                    $result[$parsed_start_date->format("Y-m-d")] = array("date" => $parsed_start_date->format("Y-m-j"), "total_steps" => 0);
                    $parsed_start_date->addDay();
                }
                $data = StepsDetail::where("userId", $user_id)->whereRaw("(stepDate >= '" . $request->input("start_date") . "' AND stepDate <= '" . $request->input("end_date") . "')")->orderBy("stepDate", "asc")->get();
                foreach (!$data->isEmpty() ? $data : array() as $d) {
                    $result[$d->stepDate]["total_steps"] = $d->totalSteps;
                }
            } else if ($duration == "day") {
                $data = StepsDetail::where("userId", $user_id)->where("stepDate", $request->input("start_date"))->first();
                $slot_array = array("12am-6am" => "0-6", "6am-12pm" => "6-12", "12pm-6pm" => "12-18", "6pm-12am" => "18-0");
                foreach ($slot_array as $k => $v) {
                    $result[$k] = array("date" => $v, "total_steps" => 0);
                }
                if ($data) {
                    $existing_slot_data = json_decode($data->hourwiseSteps, true);
                    foreach ($existing_slot_data as $es => $value) {
                        $result[$es]["total_steps"] = $value;
                    }
                }
            }
            return $this->sendResultJSON("1", "", array("data" => array_values($result)));
        } catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
}
