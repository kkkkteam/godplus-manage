<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChurchMember;

class AdminContorller extends Controller
{
    
    //-----------------------------------------------------------
    public function joinMemberView(Request $request)  {
        
        $mobile = $request->input("_m") ?? "";

        return view('join_member',["mobile" => $mobile]);
    }
    
    //-----------------------------------------------------------
    public function successMemberView(Request $request)  {
        
        $name = $request->name ?? "";

        return view('success_member',["name" => $name]);
    }
    
    //-----------------------------------------------------------
    public function joinMemberPI(Request $request)  {

        $array = ChurchMember::createMember($request->except("mobile"), $request->mobile);
        $response["status"] = $array["status"];
        $name = $array["member"]->nickname ?? $array["member"]->surname_zh;

        if ($array["status"] == 0) {           

            $response["name"] = $name;
            $response["url"] = route('member.success.html', ["name"=>$name]);

        } elseif ($array["status"] == 1) {

            $response["error"] = $name." 在記錄中已有你的登記，可以再檢查一下資料。";

        } else{

            $response["error"] = "抱歉暫時無法接受登記，請稍後再試";
        }

        return response()->json($response);
    }

    
}
