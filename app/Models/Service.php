<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;
    protected $guarded = [];
    //-------------------------------------------------
    public static function getAfterNowList(){
        $list = self::where("start_at", ">", Carbon::now())->get();
        return $list ;
    }

    //-------------------------------------------------
    public static function createService($informationArray){

        do{
            $slug = Str::random(16);
            $serviceSlugExist = self::where("slug", $slug)->first();
        }while($serviceSlugExist);

        $service = new self;
        $service->slug = $slug;
        $service->title = $informationArray["title"];
        $service->speaker = $informationArray["speaker"];

        $carbon_start= Carbon::parse($informationArray["start_date"]." ".$informationArray["start_time"]);
        $service->start_at = $carbon_start;
        $service->end_at = date("Y-m-d H:i:s", strtotime("+2 hours", strtotime($carbon_start) ));

        $carbon_public= Carbon::parse($informationArray["public_date"]." ".$informationArray["public_time"]);
        $service->public_at = $carbon_public;

        $service->save();

        return $service;
    }
    
}
