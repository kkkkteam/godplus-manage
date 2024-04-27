<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\ServiceRegistation;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

use App\Models\WhatsappWebhook;
use App\Models\WhatsappSendOut;
use App\Models\StatusMessage;
use App\Models\ChatCommand;
use App\Models\ChurchMember;

//-----------------------------------------------------------
class WhatsppController extends Controller
{

    //-----------------------------------------------------------
    public function inboundMessageAPI(Request $request)  {

		// {
		// 	"SmsMessageSid": "SMba01076f7c52da2596b81f93d9d58101",
		// 	"NumMedia": "0",
		// 	"ProfileName": "Kay Ng",
		// 	"MessageType": "text",
		// 	"SmsSid": "SMba01076f7c52da2596b81f93d9d58101",
		// 	"WaId": "85297359017",
		// 	"SmsStatus": "received",
		// 	"Body": "Testing and work for gp",
		// 	"To": "whatsapp:+14155238886",
		// 	"NumSegments": "1",
		// 	"ReferralNumMedia": "0",
		// 	"MessageSid": "SMba01076f7c52da2596b81f93d9d58101",
		// 	"AccountSid": "ACee09d2a5c91f189f5eb3e49c6b0e9cce",
		// 	"From": "whatsapp:+85297359017",
		// 	"ApiVersion": "2010-04-01"
		// }

		$sender = $request->From;
		$receiver = $request->To;
		$bodyMessage = $request->Body;
		$webhook = WhatsappWebhook::create([
			"to" 			=> str_replace("whatsapp:+","",$sender),
			"from" 			=> str_replace("whatsapp:+","",$receiver),
			"message_type" 	=> $request->MessageType,
			"sms_sid" 		=> $request->SmsSid,
			"status" 		=> $request->SmsStatus,
			"body" 			=> $bodyMessage,
			"profile_name" 	=> $request->ProfileName,
			"content" 		=> json_encode($request->all(), JSON_UNESCAPED_UNICODE),
		]);

		$messageOutArray = $this->messageLogic(str_replace("whatsapp:+","",$sender),$bodyMessage );
		if (isset($messageOutArray["mediaUrl"]) ) {
			$messageArray = [
				"From" 	=> $receiver,
				"To" 	=> $sender,
				"Body" 	=> $messageOutArray["text"],
				"MediaUrl" 	=> $messageOutArray["mediaUrl"],
			];
		}else{
			$messageArray = [
				"From" 	=> $receiver,
				"To" 	=> $sender,
				"Body" 	=> $messageOutArray["text"],
			];
		}

		$response = $this->sendingAction($messageArray);

		if($response){

			// {
			// 	"account_sid": "ACee09d2a5c91f189f5eb3e49c6b0e9cce",
			// 	"api_version": "2010-04-01",
			// 	"body": "God+ ud83dude03 u661fu671fu516d3:00pmu671fu5f85u898bu4f60",
			// 	"date_created": "Sun, 21 Apr 2024 06:14:47 +0000",
			// 	"date_sent": null,
			// 	"date_updated": "Sun, 21 Apr 2024 06:14:47 +0000",
			// 	"direction": "outbound-api",
			// 	"error_code": null,
			// 	"error_message": null,
			// 	"from": "whatsapp:+14155238886",
			// 	"messaging_service_sid": null,
			// 	"num_media": "0",
			// 	"num_segments": "1",
			// 	"price": null,
			// 	"price_unit": null,
			// 	"sid": "SM1b2526582521ae417f4fa3fed3f13d46",
			// 	"status": "queued",
			// 	"subresource_uris": {
			// 	  "media": "/2010-04-01/Accounts/ACee09d2a5c91f189f5eb3e49c6b0e9cce/Messages/SM1b2526582521ae417f4fa3fed3f13d46/Media.json"
			// 	},
			// 	"to": "whatsapp:+85297359017",
			// 	"uri": "/2010-04-01/Accounts/ACee09d2a5c91f189f5eb3e49c6b0e9cce/Messages/SM1b2526582521ae417f4fa3fed3f13d46.json"
			//   }

			WhatsappSendOut::create([
				"from" 			=> str_replace("whatsapp:+","",$sender),
				"to" 			=> str_replace("whatsapp:+","",$receiver),
				"body" 			=> json_decode($response, true)["body"],
				"content" 		=> $response, //JSON format
			]);

		}
	}

    //-----------------------------------------------------------
    public function statusAPI(Request $request)  {

		$status = StatusMessage::create([
			"content" => json_encode($request->all(), JSON_UNESCAPED_UNICODE),
		]);

		return $status;

	}

	//-----------------------------------------------------------
	public function sendingAction($array)  {

		$sid    = env('TWILIO_ACCOUNT_SID','');
		$tokenEn = env('TWILIO_AUTH_TOKEN_BASE64','');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/".$sid."/Messages.json");
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$tokenEn));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $array);
		$response = curl_exec($curl);
		curl_close($curl);

		return $response;

	}

	//-----------------------------------------------------------
	public function messageLogic($mobile, $message)  {
		// template : $message = 請給我入場CODE:Plf8jF9GSIfRgdrl
		$serviceWord = "請給我入場CODE";
		if (str_contains( $message ,$serviceWord) === true) {
			$msgPieces = explode(":", $message);
			$attendanceIDList = ServiceRegistation::where("service_slug", $msgPieces[1])
								->where(function($list) use ($mobile)  {
									$list->where("mobile", $mobile)->orWhere("recommend_by_mobile", $mobile);
								})->pluck("id")->toArray();

			$token = $mobile."_".implode("-", $attendanceIDList)."_".$msgPieces[1]; // Replace with your actual token data
			$qrCode = QrCode::format('png')->size(200)->generate($token);
			$filePath = storage_path('app/public/qrcodes/' . $token . '.png');
			file_put_contents($filePath, $qrCode);
			$arrayMessage = $arrayMessage['mediaUrl'] = [$filePath];
			$messageOut = "Welcome 請向招待員出示🥰";
	
		} else {
			$resultMessage = ChatCommand::where("command", $message)->first();
			if($resultMessage){
				$member = ChurchMember::where("mobile", $mobile)->first();

				// ___NAME___
				if ($member){
					$name = $member->nickname ?? $member->surname_zh;
					$messageOut = str_replace('__NAME__', $name, $resultMessage->reply_with_name);
				}else{
					$messageOut = $resultMessage->reply;
				}

				// __MOBILE__
				$messageOut = str_replace('__MOBILE__',substr($mobile, 3, 8), $messageOut);

				// __SERVICE__
				if (strstr($messageOut,'__SERVICE__' ) !== false) {
					$serviceList = ServiceController::checkMemberRegistration($mobile);
					$messageOut = str_replace('__SERVICE__', $serviceList , $messageOut);
				}
			}
		}

		$arrayMessage["text"] = $messageOut;
dd($arrayMessage);
		return $arrayMessage;

	}

}
