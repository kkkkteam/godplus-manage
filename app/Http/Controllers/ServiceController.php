<?php

namespace App\Http\Controllers;

use App\Models\ChurchMember;
use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\ServiceRegistation;
use App\Models\ServiceAttendance;

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
                $time = date("d/m h:ia", strtotime($row->created_at));
                $dataArray[] = array(
                    $count,
                    $time,
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
    public function showRegistrationListView(Request $request){

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
    public function scannerView(Request $request){
        return view("admin.service.scan");
    }

    //-----------------------------------------------------------
    public function showRegistrationListAPI(Request $request){

        $code = $request->body ? json_decode($request->code, true) : "" ;
        $array = explode("_",$code);
        $idArray = explode("-",$array[1]);

        $list = ServiceRegistation::where("service_slug", $array[2])
                                    ->whereIn("id", $idArray)
                                    ->get();
        if(empty($list)){
            $result["status"] = -1;
            $result["message"] = "沒有記錄";
            return response()->json($result);
        }

        $html = '<form id="myForm">';
        foreach ($list as $row) {
            $new = $row->is_newcomer == true ? "(新朋友)" : "" ;
            switch ($row->age_range) {
                case "bady":                $age = "[0-3歲]";   break;
                case "kindergarten":        $age = "[幼稚園]";   break;
                case "primary":             $age = "[小學]";    break;
                case "junior-high-school":  $age = "[初中]";    break;
                case "high-school":         $age = "[高中]";    break;
                case "college":             $age = "[大專/大學]"; break;
                case "adult":               $age = "[在職]"; break;
                case "elderly":             $age = "[長者]"; break;
                default:                    $age = ""; break;
            }
            $html .= '<div><input type="checkbox" value="'.$row->id.'" name="myCheckbox" checked /><label for="'.$row->id.'">'.$row->name.$age.$new.'</label></div>';
        }
        
        $result=[];
        $result["status"] = 0;
        $result["slug"] = $array[2];
        $result["html"] = $html.'</form>';
        return response()->json($result);

    }

    //-----------------------------------------------------------
    public function registrationListDetailsView(Request $request){

        $serviceList = Service::where("start_at", ">",Carbon::now())->select("slug", "start_at","title")->get()->toArray();
        $array = [];
        foreach($serviceList as $service){
            $array[]=[
                "slug"     => $service["slug"],
                "title"     => $service["title"],
                "start_at"  => date("Y-m-d h:ia", strtotime($service["start_at"])),
            ];
        }

        return view("admin.service.registration-details",[
            "serviceList" => $array,
        ]);

    }

    //-----------------------------------------------------------
    public function registrationListDetailsAPI(Request $request){

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
                $age = "";
                switch ($row->age_range) {
                    case "bady":                $age = "0-3歲";   break;
                    case "kindergarten":        $age = "幼稚園";   break;
                    case "primary":             $age = "小學";    break;
                    case "junior-high-school":  $age = "初中";    break;
                    case "high-school":         $age = "高中";    break;
                    case "college":             $age = "大專/大學"; break;
                    case "adult":               $age = "在職"; break;
                    case "elderly":             $age = "長者"; break;
                    default:                    $age = ""; break;
                }

                if(strlen($age)==0){
                    $member = ChurchMember::where("mobile", "852".$row->mobile)->first();
                    if ($member && !is_null($member->birthday)){
                        $memberAge = Carbon::parse($member->birthday)->age;
                        if ($memberAge > 60 ){
                            $age = "長者";
                        }elseif($memberAge > 23 ) {
                            $age = "在職";
                        }else{
                            $age = $memberAge." 歲";
                        }
                    }
                }

                $dataArray[] = array(
                    $count,
                    $row->name,
                    $age,
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
    public function makeAttendanceListAPI(Request $request){

        $attendanceIDList = $request->input("arrayID");
        $slug = $request->input("slug");
        $messageOut = ServiceAttendance::makeAttendanceById($attendanceIDList, $slug);

        $response["status"] = 0 ;
        $response["message"] = $messageOut ;
        return response()->json($response);
        
    }

}
