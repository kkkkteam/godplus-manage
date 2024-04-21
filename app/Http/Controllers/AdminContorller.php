<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminContorller extends Controller
{
    
    //-----------------------------------------------------------
    public function joinMemberView(Request $request)  {
        
        $mobile = $request->mobile ?? "";

        view ('join_member',[
            "mobile" => $mobile
        ]);
    }
    

    //-----------------------------------------------------------
    public function joinMemberAPI(Request $request)  {
    
    }

}
