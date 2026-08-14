<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\AppUserDetail;

class APIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //First it will check header data.
        if ($request->header('Authorization')) {
            //It will decode token and then parse to check valid request or not.
            $token = base64_decode(base64_decode($request->header('Authorization')));
            if ($token != "") {
                $token_parts = json_decode($token, true);
                if (is_array($token_parts) && count($token_parts) == 3) {
                    if ($token_parts['timestamp'] <= Carbon::now()->addHour()->timestamp) {
                        if ($token_parts['type'] == "app") {
                            $user_detail = AppUserDetail::where("id", $token_parts["user_id"])->first();
                            if (!$user_detail) {
                                return response()->json(['ResponseCode' => "11", 'ResponseText' => "Unauthorised"], 200);
                            } else {
                                $user_detail->request_type = $token_parts["type"];
                                $user_detail->is_app = ($token_parts["type"] == "app" ? 1 : 0);
                                session(['user_details' => $user_detail]);
                                return $next($request);
                            }
                        }
                    }
                }
            }
        }
        return response()->json(['ResponseCode' => "11", 'ResponseText' => "Unauthorised"], 200);
    }
}
