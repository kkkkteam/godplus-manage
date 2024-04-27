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
            $carbon_start= Carbon::parse($request->start_date." ".$request->start_time);
            $service->start_at = $carbon_start;
            $service->end_at = date("Y-m-d H:i:s", strtotime("+2 hours", strtotime($carbon_start) ));
            $service->save();

            $response["status"] = 0;
            $response["url"] = route("admin.service.list.html");
        }

		return response()->json($response);
    }

    
    //-----------------------------------------------------------
    public function serviceRegisterView(Request $request)  {

        $mobile = $request->input("m") ?? "";

        $serviceList = Service::where("start_at", ">",Carbon::now())->select("slug", "start_at","title")->get()->toArray();
        $array = [];
        foreach($serviceList as $service){
            $array[]=[
                "slug"     => $service["slug"],
                "title"     => $service["title"],
                "start_at"  => date("Y-m-d h:ia", strtotime($service["start_at"])),
            ];
        }

        return view("join_service", [
            "mobile" => $mobile,
            "serviceList" => $array
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

        $alreadyApplied =  ServiceRegistation::where("mobile", "852".$mobile)->where("service_slug", $serviceSlug)->first();

        if (!$alreadyApplied){
            $member = ChurchMember::where("mobile", "852".$mobile)->first();
            if ($member) {
                ServiceRegistation::create([
                    "name" => $member->lastname_zh.$member->surname_zh,
                    "mobile" => $member->mobile,
                    "service_slug" => $serviceSlug,
                ]);
                $name = $member->lastname_zh.$member->surname_zh;
            } else {
                $memberNew = ChurchMember::createMemberByService($name, $mobile);
                ServiceRegistation::create([
                    "name" => $memberNew["member"]->nickname,
                    "mobile" => $memberNew["member"]->mobile,
                    "service_slug" => $serviceSlug,
                ]);
            }
        }

        $friendConunt = 0;
        foreach( $friendDictionary as $friend){
            if (strlen($friend["name"])==0) {continue;}
            ServiceRegistation::create([
                "name" => $friend["name"],
                "recommend_by_name" => $name,
                "recommend_by_mobile" => "852".$mobile,
                "age_range" => $friend["age"],
                "is_newcomer"=> $friend["is_newcomer"] == "true" ? 1 : 0,
                "service_slug" => $serviceSlug,
            ]);
            $friendConunt++;
        }
        $response["status"] = 0;
        $response["url"] = route("member.service.success.html");
        if ($friendConunt>0){
            $response["message"] = $name."和你".$friendConunt."位家人/朋友, 到時見😃";
        }else{
            $response["message"] = $name." 到時見😃";
        }

        return response()->json($response);
    }

    //-----------------------------------------------------------
    public function serviceRegisterSuccessView(Request $request){
        $message = "查看我的報名記錄";
        $sender = env("TWILIO_WHATSAPP_MOBILE","");
        $url = "https://wa.me/".$sender."?text=".$message;
        return view("success_service",[
            "url" => $url,
        ]);
    }

    //-----------------------------------------------------------
    public static function checkMemberRegistration($mobile){

        $reply = "沒有記錄";
        if (strlen($mobile) == 0) { return  $reply;}

        $validServiceList = Service::where("start_at", ">",Carbon::now())->get();
        if (count($validServiceList) == 0) {return  $reply;}

        $validServiceSlugArray = $validServiceList->select("slug")->toArray();
        $registionServiceList = ServiceRegistation::where("mobile", $mobile)->whereIn("service_slug", $validServiceSlugArray)->get();
        if (count($registionServiceList) == 0) { return  $reply;}

        $count = 1;
        $reply = "";
        foreach($registionServiceList as $service){

            $reply .= "\n";

            $registionServiceTogether = ServiceRegistation::where("recommend_by_mobile", $mobile)->where("service_slug",$service->service_slug)->get();
            $serviceDetail = Service::where("slug", $service->service_slug )->first();
            $reply .= $count.". ".$serviceDetail->start_at." 《".$serviceDetail->title."》\n";
            if (count($registionServiceTogether)>0){
                $reply .= "\t\t同行: ";
                foreach($registionServiceTogether as $people){
                    $reply .= $people->name." ";
                }
            }
            $count++;
        }

        return $reply;
    }

    //-----------------------------------------------------------
    public function registrationListView(Request $request){

        $serviceList = Service::where("start_at", ">",Carbon::now())->select("slug", "start_at","title")->get()->toArray();
        $array = [];
        foreach($serviceList as $service){
            $array[]=[
                "slug"     => $service["slug"],
                "title"     => $service["title"],
                "start_at"  => date("Y-m-d h:ia", strtotime($service["start_at"])),
            ];
        }

        return view("admin.service.registration",[
            "serviceList" => $array,
        ]);
    }

    //-----------------------------------------------------------
    public function registrationListAPI(Request $request){

        $serviceSlug = $request->input("slug") ?? "";
        if(strlen($serviceSlug) == 0){
            $recetService = Service::where("start_at", ">",Carbon::now())->orderBy("start_at", "asc")->first();
            $serviceSlug = $recetService->slug;
        }
       
        $dataArray = array();
        if(strlen($serviceSlug) > 0){
            $list = ServiceRegistation::where("service_slug", $serviceSlug)->get();
            $count = 1;
            foreach ($list as $row)  {
                $isNewcomer = $row->is_newcomer > 0 ? "Yes" : "";
                $refereer = $row->recommend_by_name ?? "";
                $dataArray[] = array(
                    $count,
                    $row->created_at->toDateTimeString(),
                    $row->name,
                    $isNewcomer,
                    $refereer,
                );
                $count++;
            }
        }

		//  Output now
		$response["data"] = $dataArray;
		return response()->json($response);
    
    }

    //-----------------------------------------------------------
    public function scannerView(Request $request){
        return view("admin.service.scan");
    }


}
