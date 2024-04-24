<?php

namespace App\Http\Controllers;

use App\Models\ChurchMember;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceRegistation;

use Carbon\Carbon;

class ServiceController extends Controller
{

    //-----------------------------------------------------------
    public function serviceListView(Request $request)  {
        return view("admin.service.list");
    }

    //-----------------------------------------------------------
    public function getServiceListAPI(Request $request)  {

        $array = Service::getAfterNowList();

		$dataArray = array();
		foreach ($array as $row)  {

			$dataArray[] = array(
				$row->id,
                $row->slug,
                $row->start_at,
				$row->title,
				$row->speaker,
			);
		}

		//  Output now
		$response["data"] = $dataArray;
		return response()->json($response);

    }
    //-----------------------------------------------------------
    public function addServiceAPI(Request $request)  {
        
        $response = [];
        $response["status"] = -1;

        $service = Service::createService($request->all());
        if ($service){
            $response["status"] = 0;
        }
		return response()->json($response);
    }

    //-----------------------------------------------------------
    public function updateServiceView(Request $request)  {
    
        $service = Service::where("slug",$request->slug)->first();

        if($service){
            $date = substr($service->start_at, 0, 10);
            $time = substr($service->start_at, 11, 10);

            return view("admin.service.update",[
                "service" => $service,
                "date" => $date,
                "time" => $time,
            ]);
        }

        return view("admin.service.list");

    }
    
    //-----------------------------------------------------------
    public function updateServiceActionAPI(Request $request)  {

        $service = Service::where("slug",$request->slug)->first();
        if ($service) {
            $result["status"] = 0;
            $result["url"] = route("admin.service.update.html", ["slug" => $service->slug]);
        }else{
            $result["status"] = -1;
        }

        return response()->json($result);

    }

    //-----------------------------------------------------------
    public function updateServiceAPI(Request $request)  {

        $response = [];
        $response["status"] = -1;

        $service = Service::where("slug",$request->slug)->first();
        if($service){

            $service->title = $request->title;
            $service->speaker = $request->speaker;
            $carbon_start= Carbon::parse($request->date." ".$request->time);
            $service->start_at = $carbon_start;
            $service->end_at = $carbon_start->addHours(2);
            $service->save();

            $response["status"] = 0;
            $response["url"] = route("admin.service.list.html");
        }

		return response()->json($response);
    }

    
    //-----------------------------------------------------------
    public function serviceRegisterView(Request $request)  {

        $mobile = $request->input("m") ?? "";

        $serviceList = Service::where("start_at", ">",Carbon::now())->get();

        return view("join_service", [
            "mobile" => $mobile,
            "serviceList" => $serviceList
        ]);

    }
    
    //-----------------------------------------------------------
    public function serviceRegisterAPI(Request $request)  {

        $response = [];
        $response["status"] = -1;

        $name = $request->name?? "";
        $mobile = $request->mobile ?? "";
        $serviceSlug = $request->service ?? "";
        $friendDictionary = $request->friends;

        if (strlen($name) == 0 || strlen($mobile) == 0 || strlen($serviceSlug) == 0) {
            $response["error"] = "資料不足，請仔細看一下，再呈交。";
            return response()->json($response);
        }

        $member = ChurchMember::where("mobile", $mobile)->first();
        if ($member) {
            ServiceRegistation::create([
                "name" => $member->lastname_zh.$member->surname_zh,
                "mobile" => $member->mobile,
                "serivce_slug" => $serviceSlug,
            ]);
            $nameOfApplicatant = $member->lastname_zh.$member->surname_zh;
        } else {
            $memberNew = ChurchMember::createMemberByService($name, $mobile);
            ServiceRegistation::create([
                "name" => $memberNew["member"]->nickname,
                "mobile" => $memberNew["member"]->mobile,
                "serivce_slug" => $serviceSlug,
            ]);
            $nameOfApplicatant = $memberNew["member"]->nickname;
        }

        foreach( $friendDictionary as $friend){
            ServiceRegistation::create([
                "name" => $friend["name"],
                "recommend_by_name" => $nameOfApplicatant,
                "recommend_by_mobile" => "852".$mobile,
                "age_range" => $friend["age"],
                "is_newcomer"=> $friend["is_newcomer"] ? 1 : 0,
                "serivce_slug" => $serviceSlug,
            ]);
        }
        $response["status"] = 0;
        if (count($friendDictionary)>0){
            $response["message"] = $nameOfApplicatant."和你".count($friendDictionary)."同行者, 到時見😃";
        }else{
            $response["message"] = $nameOfApplicatant." 到時見😃";
        }

        return response()->json($response);
    }


}
