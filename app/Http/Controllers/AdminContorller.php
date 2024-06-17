<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EmailController;


use Illuminate\Http\Request;
use App\Models\ChurchMember;
use App\Models\ChatCommand;
use App\Models\ChatWelcomeCommand;

class AdminContorller extends Controller
{
    
    //-----------------------------------------------------------
    public function joinMemberView(Request $request)  {
        
        $mobile = $request->input("m") ?? "";

        return view('join_member',["mobile" => $mobile]);
    }
    
    //-----------------------------------------------------------
    public function successMemberView(Request $request)  {
        
        $name = $request->name ?? "";

        return view('success_member',["name" => $name]);
    }
    
    //-----------------------------------------------------------
    public function joinMemberPI(Request $request)  {

        $array = ChurchMember::createMember($request->except("mobile"), $request->mobile);
        $response["status"] = $array["status"];
        $name = $array["member"]->nickname ?? $array["member"]->surname_zh;

        if ($array["status"] == 0) {           

            $response["name"] = $name;
            // $response["url"] = route('member.success.html', ["name"=>$name]);

            // $email = EmailController::sendNewMemberEmail($name, $request->email);

        } elseif ($array["status"] == 1) {

            $response["error"] = $name." 在記錄中已有你的登記，可以再檢查一下資料。";

        } else{

            $response["error"] = "抱歉暫時無法接受登記，請稍後再試";
        }

        return response()->json($response);
    }

    //-----------------------------------------------------------
    public function commandListView(Request $request)  {
        return view("admin.whatsapp.list");
    }

    //-----------------------------------------------------------
    public function getCommandListAPI(Request $request)  {

        $array = ChatCommand::getList();

		$dataArray = array();
		foreach ($array as $row)  {

			$dataArray[] = array(
				$row->id,
				$row->command,
                str_replace(array("\r", "\n", "\r\n", "\n\r"), '<br>', $row->reply),
                str_replace(array("\r", "\n", "\r\n", "\n\r"), '<br>', $row->reply_with_name),
			);
		}

		//  Output now
		$response["data"] = $dataArray;
		return response()->json($response);
    }

    //-----------------------------------------------------------
    public function setCommandAPI(Request $request)  {

        $command = ChatCommand::create([
            "command" => $request->command,
            "reply" => $request->reply,
            "reply_with_name"=> $request->reply_with_name
        ]);

        if ($command) {
            $response["status"] = 0;
        }else{
            $response["status"] = -1;
        }

        return response()->json($response);
    }

    //-----------------------------------------------------------
    public function deleteCommandAPI(Request $request)  {

        $del = ChatCommand::where('id',$request->id)->delete();

        if ($del) {
            $response["status"] = 0;
        }else{
            $response["status"] = -1;
        }
        return response()->json($response);

    }

    
    //-----------------------------------------------------------
    public function updateCommandView(Request $request)  {

        $command = ChatCommand::where("id",$request->id)->first();

        if ($command) {

            return view("admin.whatsapp.update",[
                "whatsapp" => $command,
            ]);

        }else{
            return view("admin.whatsapp.list");
        }

    }

    //-----------------------------------------------------------
    public function updateCommandAPI(Request $request)  {

        $command = ChatCommand::where("id",$request->id)->first();

        $result=[];
        
        if ($command) {
            $command->command = $request->command;
            $command->reply = $request->reply;
            $command->reply_with_name = $request->reply_with_name;
            $command->save();

            $result["status"] = 0;
            $result["url"] = route("admin.command.list.html");
        }else{
            $result["status"] = -1;
        }

        return response()->json($result);

    }

    
    //-----------------------------------------------------------
    public function updateActionAPI(Request $request)  {

        $command = ChatCommand::where("id",$request->id)->first();
        if ($command) {
            $result["status"] = 0;
            $result["url"] = route("admin.command.update.html", ["id" => $command->id]);
        }else{
            $result["status"] = -1;
        }

        return response()->json($result);

    }

    //-----------------------------------------------------------
    public function getWelcomeMessageListAPI(Request $request)  {

        $array = ChatWelcomeCommand::getList();

		$dataArray = array();
		foreach ($array as $row)  {

            $str = "";
            switch($row->id){
                case 1:
                    $str = "第1次";
                    break;
                case 2:
                    $str = "第2-3次";
                    break;
                case 3:
                    $str = "第4次";
                    break;
                default:
                    $str = "會眾";
                    break;
            }

			$dataArray[] = array(
                $row->id,
				$str,
                str_replace(array("\r", "\n", "\r\n", "\n\r"), '<br>', $row->message),
			);
		}

		//  Output now
		$response["data"] = $dataArray;
		return response()->json($response);

    }
    
    //-----------------------------------------------------------
    public function updateWelcomeMessageActionAPI(Request $request)  {

        $command = ChatWelcomeCommand::where("id",$request->id)->first();
        if ($command) {
            $result["status"] = 0;
            $result["url"] = route("admin.command.welcome.update.html", ["id" => $command->id]);
        }else{
            $result["status"] = -1;
        }

        return response()->json($result);
        
    }

    //-----------------------------------------------------------
    public function updateWelcomeMessageView(Request $request)  {

        $command = ChatWelcomeCommand::where("id",$request->id)->first();

        if ($command) {

            $str = "";
            if ($request->id == 1){
                $str = "第1次";
            } elseif ($request->id == 2){
                $str = "第2-3次";
            } elseif ($request->id == 3){
                $str = "第4次";
            } else{
                $str = "會眾";
            }

            return view("admin.whatsapp.welcome_update",[
                "id" => $request->id,
                "times" => $str,
                "command" => $command->message,
            ]);

        }else{
            return view("admin.whatsapp.list");
        }
    
    }

    //-----------------------------------------------------------
    public function updateWelcomeMessageAPI(Request $request)  {

        $command = ChatWelcomeCommand::where("id",$request->id)->first();

        $result = [];

        if ($command) {
            $command->message = $request->command;
            $command->save();

            $result["status"] = 0;
            $result["url"] = route("admin.command.list.html");
        }else{
            $result["status"] = -1;
        }

        return response()->json($result);

    }


}
