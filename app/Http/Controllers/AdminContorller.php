<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChurchMember;

class AdminContorller extends Controller
{
    
    //-----------------------------------------------------------
    public function joinMemberView(Request $request)  {
        
        // $mobile = $request->mobile ?? "";
        $mobile = "98001234";

        return view('join_member',["mobile" => $mobile]);
    }
    

    //-----------------------------------------------------------
    public function joinMemberPI(Request $request)  {

        $member = ChurchMember::createMember($request->all(), $request->mobile);
        
        if ($member) {
            $response["status"] = 0;
            $response["name"] = $member->surname_zh;
        } else {
            $response["status"] = 1;
            $response["name"] = "";
        }

        return response()->json($response);
    }

    
}
