<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Str;

class ChurchMember extends Eloquent
{
    use HasFactory;
    protected $guarded = []; 
    
    public static function createMember($informationArray, $mobile)  {

        $member = ChurchMember::where("mobile", $mobile)->first();

        if (!$member) {
            do{
                $slug = Str::random(16);
                $memberExist = ChurchMember::where("slug", $slug)->first();
            }while($memberExist);

            $member = new self;
            $member->fill($informationArray);
            $member->slug = $slug;
            $member->save();
        }
        
        return $member;
    }
}
