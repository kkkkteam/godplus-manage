<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Str;

class ChurchMember extends Eloquent
{
    use HasFactory;
    protected $guarded = []; 
    
    // ------------------------------------------------------------------------------------------
    public static function createMember($informationArray, $mobile)  {

        if (strlen($mobile) == 8){
            $mobile = "852".$mobile;
        }
        
        $member = ChurchMember::where("mobile", $mobile)->first();
        $result = [];
        $result["status"] = -1;

        if (!$member) {
            do{
                $slug = Str::random(16);
                $memberExist = ChurchMember::where("slug", $slug)->first();
            }while($memberExist);

            $member = new self;
            $member->fill($informationArray);
            $member->slug   = $slug;
            $member->mobile = $mobile;
            $member->save();

            $result["status"] = 0;
        }else{
            // already have reocrd
            $member->fill($informationArray);
            $member->save();
            $result["status"] = 0;
        }

        $result["member"] = $member;

        return $result;
    }

        // ------------------------------------------------------------------------------------------
        public static function createMemberByService($nickname, $mobile)  {

            if (strlen($mobile) == 8){
                $mobile = "852".$mobile;
            }
        
            $member = ChurchMember::where("mobile", $mobile)->first();
            $result = [];
            $result["status"] = -1;
    
            if (!$member) {
                do{
                    $slug = Str::random(16);
                    $memberExist = ChurchMember::where("slug", $slug)->first();
                }while($memberExist);
    
                $member = new self;
                $member->nickname = $nickname;
                $member->slug   = $slug;
                $member->mobile = $mobile;
                $member->save();
    
                $result["status"] = 0;
            }else{
                // already have reocrd
                $result["status"] = 1;
            }
    
            $result["member"] = $member;
    
            return $result;
        }
}
