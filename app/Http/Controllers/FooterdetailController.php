<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FooterContentDetail;
use App\Models\ContactUsDetail;
use Illuminate\Http\Request;
use Validator;

class FooterdetailController extends Controller
{
    public function editAboutus()
    {
        $descriptions = FooterContentDetail::select("content")->where("type", "about_us")->first();
        $description_decode = json_decode($descriptions->content);
        // dd($description_decode);
        $description = $description_decode->description;
        // dd($description);
        return view('footerdetail/aboutus', compact('description'));
    }

    public function editTermsofuse()
    {
        $descriptions = FooterContentDetail::select("content")->where("type", "terms_of_use")->first();
        $description_decode = json_decode($descriptions->content);
        // dd($description_decode);
        $description = $description_decode->description;
        // dd($description);
        return view('footerdetail/termsofuse', compact('description'));
    }

    public function editPrivacypolicy()
    {
        $descriptions = FooterContentDetail::select("content")->where("type", "privacy_policy")->first();
        $description_decode = json_decode($descriptions->content);
        // dd($description_decode);
        $description = $description_decode->description;
        // dd($description);
        return view('footerdetail/privacypolicy', compact('description'));
    }

    public function updateDescription(Request $request)
    {
        try {
            $content = request("description");
            //dd($content);
            $type = request("type");
            // dd($type);
            $descriptions = FooterContentDetail::select("content")->where("type", "$type");
            //dd($descriptions);

            $descriptions->update(array(
                "content" => json_encode(array("description" => $request->input("description"))),
            ));

            if (!$descriptions) {
                return view("errors.403");
            }

            $route_name = "edit-about-us";

            if ($type == "terms_of_use") {
                $route_name = "edit-terms-of-use";
            }
            if ($type == "privacy_policy") {
                $route_name = "edit-privacy-policy";
            }

            return redirect()->route("$route_name")->with("success", "Description updated successfully");
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function  loadAddContact()
    {
        $content = ContactUsDetail::first()->toArray();
        return view('footerdetail/contactus', compact('content'));
    }

    public function insertContactus(Request $request)
    {
        try {
            $content = ContactUsDetail::first();

            $element_array = array(
                'email_id' => 'required|email',
                'about_us_email' => 'required|email',
                'phone' => 'required',
                'place_an_ad_phone' => 'required',
                'office_days' => 'required',
                'office_hours' => 'required',
                'mailing_address' => 'required',
            );
            $validator = Validator::make($request->all(), $element_array, [
                'email_id.required' => 'Please enter email id',
                'email_id.email' => 'Invaild email id',
                'about_us_email.required' => 'Please enter email id for about us',
                'about_us_email.email' => 'Invaild email id for about us',
                'phone.required' => 'Please enter phone',
                'place_an_ad_phone.required' => 'Please enter phone for place an ad',
                'office_days.required' => 'Please enter working days',
                'office_hours.required' => 'Please enter working hours',
                'mailing_address.required' => 'Please enter address',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = $content;
            $data->email_id = $request->input("email_id");
            $data->about_us_email = $request->input("about_us_email");
            $data->phone = $request->input("phone");
            $data->place_an_ad_phone = $request->input("place_an_ad_phone");
            $data->office_days = $request->input("office_days");
            $data->office_hours = $request->input("office_hours");
            $data->mailing_address = $request->input("mailing_address");         
            $data->save();

            return redirect()->route("admin-contact-us")->with("success", "Contact us data updated successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
