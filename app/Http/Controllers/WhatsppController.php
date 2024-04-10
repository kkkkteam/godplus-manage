<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappWebhook;

//-----------------------------------------------------------
class WhatsppController extends Controller
{

    //-----------------------------------------------------------
    public function inboundMessageAPI(Request $request)  {
		$response = array(
			"timeStamp" => Date("YmdHis"),
			"apiName" => __FUNCTION__,
			"status" => -99,
			"message" => "Unexpected error...",
		);

		$webhook = WhatsappWebhook::create([
			"content" => json_encode($request->all()),
		]);

		//  Output now
		$response["status"] = 0;
		$response["message"] = "Done";
		return response()->json($response);
	}

    //-----------------------------------------------------------
    public function statusAPI(Request $request)  {
		$response = array(
			"timeStamp" => Date("YmdHis"),
			"apiName" => __FUNCTION__,
			"status" => -99,
			"message" => "Unexpected error...",
		);

		$webhook = WhatsappWebhook::create([
			"content" => json_encode($request->all()),
		]);

		//  Output now
		$response["status"] = 0;
		$response["message"] = "Done";
		return response()->json($response);
	}

}
