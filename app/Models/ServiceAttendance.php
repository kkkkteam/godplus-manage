<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ServiceRegistation;
use App\Models\ChatWelcomeCommand;
use App\Models\Service;
class ServiceAttendance extends Model
{
    use HasFactory;
    protected $guarded = [];  

    //-------------------------------------------------
    public static function makeAttendanceById($IDArray, $serviceSlug){

        $applicantName = "";
        $companyNameList = "";
        $count = 1;
        $mobile = "";

        if(count($IDArray) == 0 || strlen($serviceSlug) == 0){
            return "未有找到記錄，可找招待員幫忙點名，或即場報名👇🏼\n\nhttps://godplus-manage.site/member/service/join";
        }else{
            $registationList = ServiceRegistation::where("service_slug", $serviceSlug)
            ->whereIn("id", $IDArray)
            ->where("attended", 0)
            ->get();

            foreach($registationList as $attend){
                $attendance = new ServiceAttendance();
                $attendance->register_id = $attend->id; 
                $attendance->name = $attend->name;
                $attendance->service_slug = $serviceSlug;
                $attendance->mobile = is_null($attend->mobile) ? "" : $attend->mobile;
                $attendance->save();

                $attend->attended = true;
                $attend->save();

                if (is_null($attend->mobile)){
                    $companyNameList .= $count.". ".$attend->name;
                    $count++;
                }else{
                    $applicantName = $attend->name;
                    $mobile = $attend->mobile;
                }
            }

        }

        $attendenceTime = ServiceAttendance::where("mobile", $mobile)->count();

        if($attendenceTime > 3){        // Normal 
            $command = ChatWelcomeCommand::where("id", 3)->first();
        }elseif($attendenceTime > 1){   // 2nd-3rd time
            $command = ChatWelcomeCommand::where("id", 2)->first();
        }else{                          // 1st time
            $command = ChatWelcomeCommand::where("id", 1)->first();
        }
        $message = str_replace('__NAME__', $applicantName, $command->message);

        if($count > 1){
            $message .= "\n"."已為你和你以下的家人/朋友點名\n".$companyNameList."\n現邀請你地跟招待員入場";
        }else{
            $message .= "\n"."已為你點名，現邀請你跟招待員入場，一同敬拜神。";
        }

        return $message;
    }

        //-------------------------------------------------
        public static function addAttendance($name, $mobile, $serviceSlug, $attendenceTime){

            if (strlen($name) == 0 || strlen($attendenceTime) == 0 ){
                return false;
            }

            $service = Service::where("slug", $serviceSlug)->first();

            if ($service == null){
                return false;
            }
            
            if (strlen($mobile) > 7 ){
                $mobile = "852".$mobile;
            }else{
                $mobile = "";
            }
            

            $registRecord = ServiceRegistation::create([
                "name"          => $name,
                "mobile"        => $mobile,
                "service_slug"  => $serviceSlug,
                "attended"      => true,                
            ]);

            $attednRecord = ServiceAttendance::create([
                "name"         => $name,
                "mobile"       => $mobile ?? "",
                "service_slug" => $serviceSlug,
                "updated_at"  => $attendenceTime,
                "register_id"  => $registRecord->id,
            ]);

            return $attednRecord;
        }

}
