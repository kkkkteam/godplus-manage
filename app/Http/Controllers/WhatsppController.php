<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappWebhook;
use App\Models\WhatsappSendOut;
use App\Models\statusMessage;


//-----------------------------------------------------------
class WhatsppController extends Controller
{

    //-----------------------------------------------------------
    public function inboundMessageAPI(Request $request)  {

		$webhook = WhatsappWebhook::create([
			"to" 	=> $request->to,
			"from" 	=> $request->from,
			"message_type" => $request->message_type,
			"text" => $request->text,
			"channel" => $request->channel,
			"message_uuid" => $request->message_uuid,
			"context_status" => $request->context_status,
			"profile" => json_encode($request->profile),
		]);

		$sender = $request->from;
		$array = [];
		if (str_contains($request->text, '我想返崇拜')){
			// $array["message_type"] = "image";
			// $tempContent = [];
			// $tempContent["url"] = asset('storage/images/godplus.jpeg');
			// $tempContent["caption"] = "God+ 😃 星期六3:00pm期待見你";
			$array["message_type"] = "text";	
			$tempContent = "God+ 😃 星期六3:00pm期待見你";
		}else{
			$array["message_type"] = "text";	
			$tempContent = "The message you give me: ".$request->text;
		}

		$array[$array["message_type"]] = $tempContent;
		$array["from"] = "14157386102";
		$array["to"] = $sender;
		$array["channel"] = "whatsapp";
		$json = json_encode($array);

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://messages-sandbox.nexmo.com/v1/messages',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $json,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Accept: application/json',
				'Authorization: Basic ZmZmZTEzODM6OVNPYnQyaU4zdVdGWFNrbg=='
			),
		));

		$response = curl_exec($curl);
		if($response){
			WhatsappSendOut::create([
				"to" 	=> $array["to"],
				"from" 	=> $array["from"],
				"message_type" => $array["message_type"],
				"text" => json_encode($array[$array["message_type"]], true),
				"channel" => $array["channel"],
			]);
		}
		curl_close($curl);
		
	}

    //-----------------------------------------------------------
    public function statusAPI(Request $request)  {

		$status = statusMessage::create([
			"content" => json_encode($request->all()),
		]);

	}

}
