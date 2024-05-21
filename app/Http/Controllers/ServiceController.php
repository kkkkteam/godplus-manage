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

        $serviceList = Service::where("end_at", ">",Carbon::now())->select("slug", "start_at","title")->get()->toArray();
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
                if(!is_null($member->surname_zh) && strlen($member->surname_zh)>0){
                    $name = $member->lastname_zh.$member->surname_zh;
                }
                ServiceRegistation::create([
                    "name" => $name,
                    "mobile" => $member->mobile,
                    "service_slug" => $serviceSlug,
                ]);
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

        $validServiceList = Service::where("end_at", ">",Carbon::now())->get();
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

        $serviceList = Service::where("end_at", ">",Carbon::now())->select("slug", "start_at","title")->get()->toArray();
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
            $recetService = Service::where("end_at", ">",Carbon::now())->orderBy("start_at", "asc")->first();
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
                $time = $row->attended == true  ? Carbon::parse($row->updated_at)->toTimeString() : "";
                $dataArray[] = array(
                    $count,         //0
                    $row->name,     //1
                    $age,           //2
                    $isNewcomer,    //3
                    $refereer,      //4
                    $time,          //5
                    $row->attended, //6
                    $row->id,       //7
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

    // ---------------------------------------------------
    public function registrationIndividualAttendAPI(Request $request){

        $id = $request->input("id");
        $slug = $request->input("slug");

        $messageOut = ServiceAttendance::makeAttendanceById([$id], $slug);

        $response["status"] = 0 ;
        return response()->json($response);
        
    }

    // ---------------------------------------------------
    public function attendanceUpdateView(Request $request){

        $serviceList = Service::where("end_at", "<",Carbon::now())->select("slug", "start_at","title")->orderBy("start_at","desc")->get()->toArray();
        $array = [];
        foreach($serviceList as $service){
            $array[]=[
                "slug"     => $service["slug"],
                "title"     => $service["title"],
                "start_at"  => date("Y-m-d h:ia", strtotime($service["start_at"])),
            ];
        }

        return view("admin.service.attendance.update",[
            "serviceList" => $array,
        ]);
    }

    // ---------------------------------------------------
    public function attendancelistAPI(Request $request){

        $slug = $request->input("slug");

        $attendanceList = ServiceAttendance::where("service_slug", $slug)->get();
        $dataArray = array();

        $count = 1;
        foreach ($attendanceList as $row)  {

            $updateWithinToday = 0;

            if (date("Y-m-d", strtotime($row->created_at)) == Carbon::today()) {
                $updateWithinToday = 1;
            }

            $record = ServiceRegistation::where("id", $row->register_id)->first();

            $isNewcomer = $record->is_newcomer > 0 ? "Yes" : "No";
            $refereer = $record->recommend_by_name ?? "";

            $attendTime = date("Y-m-d h:ia", strtotime($row->updated_at));

            $dataArray[] = array(
                $count,
                $row->name,
                $isNewcomer,
                $refereer,
                $attendTime,
                $updateWithinToday,
            );

            $count++;
        }

        //  Output now
        $response["data"] = $dataArray;
        return response()->json($response);
        
    }

    // ---------------------------------------------------
    public function attendanceAddAPI(Request $request){

        $response = array(
            "status"=> -1,
        );

        $slug = $request->input("slug");
        $name = $request->input("name");
        $mobile = $request->input("mobile");
        $time = $request->input("time");

        $service = Service::where("slug", $slug)->first();
        $attendDateTime = date("Y-m-d", strtotime($service->start_at))." ".$time.":00";

        $record = ServiceAttendance::addAttendance($name, $mobile, $slug, $attendDateTime);

        $response["data"] = $record->toArray();
        $response["status"] = 0 ;
        return response()->json($response);

    }

    // ---------------------------------------------------
    public function downloadServiceQRCodeAPI(Request $request){

        $slug = $request->input("slug");

        $token = "https://wa.me/".env("TWILIO_WHATSAPP_MOBILE")."?text=我到崇拜現場:".$slug;
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->margin(2)->generate($token);
        $filePath = storage_path('app/public/' . $token . '.png');
        file_put_contents($filePath, $qrCode);
        $dateStr = date('Ymd');
        $imagePath = asset('public/storage/'. $dateStr . '.png');

        return response()->download($imagePath);
    }

    // --------------------------------------------
    public function attendanceSummaryView(Request $request){
        return view("admin.service.attendance.summary");
    }

    // --------------------------------------------
    public function attendanceSummaryAPI(Request $request){

        $serviceList = Service::where("end_at", "<", date("Y-m-d H:i:s"))->get();
        $dataArray = array();

        $count = 1;
        foreach ($serviceList as $row)  {

            $tempSlug = $row->slug;

            $registCount = ServiceRegistation::where("service_slug", $tempSlug)->where("created_at","<", Carbon::parse($row->start_at))->count();
            $attendCount = ServiceAttendance::where("service_slug", $tempSlug)->count();

            $newcomerRegistIDArray = ServiceRegistation::where("service_slug", $tempSlug)->where("is_newcomer", true)->pluck("id")->toArray();  
            $newcomerCount = ServiceAttendance::where("service_slug", $tempSlug)->whereIn("register_id", $newcomerRegistIDArray  )->count();

            $attendTime = date("Y-m-d h:ia", strtotime($row->start_at));

            $url = route("admin.service.attendance.select.html", ["service_slug" => $tempSlug]);
            // No.
            // 崇拜日期/時間
            // 講題
            // 講員
            // 報名人數
            // 出席人數
            // 新朋友人數

            $dataArray[] = array(
                $count,
                $attendTime,
                $row->title,
                $row->speaker,
                $registCount,
                $attendCount,
                $newcomerCount,
                $url,
            );

            $count++;
        }

        //  Output now
        $response["data"] = $dataArray;
        return response()->json($response);
    }

    // --------------------------------------------
    public function attendanceServiceView(Request $request, $service_slug){

        $service = Service::where("slug", $service_slug)->first();
        if(!$service) {
            return view("admin.service.attendance.summary");
        }
        $attendCount = ServiceAttendance::where("service_slug", $service_slug)->count();

        return view("admin.service.attendance.detail",[
            "service" => $service,
            "attendance" => $attendCount,
        ]);
    }

    // --------------------------------------------
    public function attendanceServiceAPI(Request $request){

        $slug = $request->input("slug");

        $attendList = ServiceAttendance::where("service_slug", $slug)->get();
        $dataArray = array();

        $count = 1;
        foreach ($attendList as $row)  {
            $record = ServiceRegistation::where("id", $row->register_id)->first();
            $isNewcomer = $record->is_newcomer == true ? 1 : 0 ;
            $dataArray[] = array(
                $count,
                $row->name,
                $record->recommend_by_name,
                $isNewcomer
            );
            $count++;
        }

        //  Output now
        $response["data"] = $dataArray;
        return response()->json($response);

    }
    
    // --------------------------------------------
    public function attendanceServiceByPoepleView(Request $request){

        $arrayTitle = [];

        $now = date("Y-m-d H:i:s") ;
        $serviceList = Service::where("end_at", "<" ,$now )->orderBy("start_at", "DESC")->select("title","start_at")->get()->toArray();

        foreach ($serviceList as $row) {
            $arrayTitle[] = date("y年m月d日", strtotime($row["start_at"]));
        }

        return view("admin.service.attendance.people",[
            "arrayTitle" => $arrayTitle,
            "serviceCount" => count($serviceList),
        ]);
    }

    // --------------------------------------------
    public function attendanceServiceByPoepleAPI(Request $request){

        $now = date("Y-m-d H:i:s") ;
        $memberWithMobileList = ServiceAttendance::where("mobile", "!=" , "")->groupBy('mobile')->pluck("mobile");
        $memberWithoutMobileList = ServiceAttendance::where("mobile",  "")->groupBy('name')->pluck("name");
        $serviceList = Service::where("end_at", "<" ,$now )->orderBy("start_at", "DESC")->get();

        $dataArray = array();

        $count = 1;
        foreach ($memberWithMobileList as $row)  {
            $record = ServiceAttendance::where("mobile", $row)->get();
            $member = ChurchMember::where("mobile", $row)->first();
            $string = $count.",";
            $string .= $member->nickname ?? " ";

            foreach ($serviceList as $service) {
                $attend = $record->where("service_slug", $service->slug)->first();
                if ($attend) {
                    $string .= ",".date("h:sa", strtotime($attend->updated_at));
                }else{
                    $string .= ", ";
                }
            }

            $dataArray[] = explode(",", $string);
            $count++;
        }


        foreach ($memberWithoutMobileList as $row)  {
            $record = ServiceAttendance::where("name", $row)->get();
            $string = $count.",";
            $string .= $row ?? " ";

            foreach ($serviceList as $service) {
                $attend = $record->where("service_slug", $service->slug)->first();
                if ($attend) {
                    $string .= ",".date("h:sa", strtotime($attend->updated_at));
                }else{
                    $string .= ", ";
                }
            }

            $dataArray[] = explode(",", $string);
            $count++;
        }

        //  Output now
        $response["data"] = $dataArray;
        return response()->json($response);

    }
    
}
