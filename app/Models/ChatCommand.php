<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatCommand extends Model
{
    use HasFactory;
    protected $guarded = [];  

    //-------------------------------------------------
    public static function getList(){
        $list = self::all();
        return $list ;
    }
}
