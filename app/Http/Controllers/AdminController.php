<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ArticleDetail;

class AdminController extends Controller
{
    /**
     *  Method to verify admin login credientials
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function loadDashboard(Request $request)
    {
        try {
            $latest_news = ArticleDetail::selectRaw("heading,publishDate")->where("status", 1)->where("isVideo", 0)->orderBy("publishDate","desc")->take(5)->get();
            return view("common.dashboard",compact("latest_news"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     *  Method to verify admin login credientials
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function vefiryLogin(Request $request)
    {
        try {
            $element_array = array(
                'user_name' => 'required',
                'password' => 'required|min:3'
            );
            $validator = Validator::make($request->all(), $element_array, [
                'user_name.required' => "Please enter user name",
                'password.required' => "Please enter password"
            ]);
            if ($validator->fails()) {
                return \Redirect::to('login')->withErrors($validator);
            }
            $user_name = $request->input("user_name");
            $password = $request->input("password");

            $data = User::where("user_name", $user_name)->where("password", md5($password))->first();
            if (!$data) {
                return back()->with("error", "User data not found");
            }
            Auth::loginUsingId($data->id);
            return \Redirect::route("dashboard")->with("success", "User login successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
