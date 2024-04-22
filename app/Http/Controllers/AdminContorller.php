<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminContorller extends Controller
{
    
    //-----------------------------------------------------------
    public function joinMemberView(Request $request)  {
        
        // $mobile = $request->mobile ?? "";
        $mobile = "98001234";

        return view('join_member');
    }
    

    //-----------------------------------------------------------
    public function createMemberAPI()  {


    
    }

    
}
