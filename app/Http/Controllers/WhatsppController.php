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
			$imageText = [];
			$imageText["url"] = public_path("godplus.jpeg");
			$imageText["caption"] = "期待星期六3pm見到你。";
			$array["image"] = $imageText;
		}else{
			$array["message_type"] = "text";
			$array["text"] = "The message you give me: ".$request->text;
		}

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
				"to" 	=> $array["from"],
				"from" 	=> $array["from"],
				"message_type" => $array["message_type"],
				"text" => $array[$array["message_type"]],
				"channel" => $array["channel"],
			]);
		}
		curl_close($curl);
		
	}

    //-----------------------------------------------------------
    public function statusAPI(Request $request)  {


		$webhook = statusMessage::create([
			"content" => json_encode($request->all()),
		]);

	}

}
