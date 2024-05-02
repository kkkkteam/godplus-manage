<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ServiceRegistation;
class ServiceAttendance extends Model
{
    use HasFactory;
    protected $guarded = [];  

    //-------------------------------------------------
    public static function makeAttendanceById($IDArray, $serviceSlug){

        if(count($IDArray) == 0 || strlen($serviceSlug) == 0){
            return "未有找到記錄，可找招待員幫忙點名，或即場報名👇🏼\n\nhttps://godplus-manage.site/member/service/join";
        }

        $registationList = ServiceRegistation::where("service_slug", $serviceSlug)
                            ->whereIn("id", $IDArray)
                            ->where("attended", 0)
                            ->get();

        $applicantName = "";
        $companyNameList = "";
        $count = 1;
        $mobile = "";

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

        $attendenceTime = ServiceAttendance::where("mobile", $mobile)->count();

        if($attendenceTime > 3){        // Normal 
            $message = $applicantName." 很高興你回到God Plus神家當中，願你多多領受神的感動。💞\n";
        }elseif($attendenceTime > 1){   // 2nd-3rd time
            $message = $applicantName." Happy to see you again~\n回到God Plus神家當中，願你今天也得著滿滿的感動。🫶\n";
        }else{                          // 1st time
            $message = $applicantName." 歡迎你第一次來到God Plus神家當中，希望你在這裡與神相遇，和經歷神家裡既愛。🥰🫰\n";
        }

        if($count > 1){
            $message .= "\n"."已為你和你以下的家人/朋友點名\n".$companyNameList."\n現邀請你地跟招待員入場";
        }else{
            $message .= "\n"."已為你點名，現邀請你跟招待員入場，一同敬拜神。";
        }

        return $message;
    }

}
