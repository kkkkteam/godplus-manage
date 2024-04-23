<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailController extends Controller
{
    //----------------------------------------------------------------------------------------
    private function PHPMailerSetUp()
	{
		$mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host = "smtp.gmail.com";
		$mail->SMTPAuth = true;
        $mail->Username = env("MAIL_USERNAME","");
		$mail->Password = env("MAIL_PASSWORD","");
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;  

		$mail->setFrom(env("MAIL_FROM_ADDRESS",""), env("MAIL_FROM_NAME",""));

		$mail->isHTML(true);
		$mail->CharSet = "UTF-8";

		return $mail;
	}


    //----------------------------------------------------------------------------------------
	public static function sendNewMemberEmail($name="", $email="")  {

		$searchArr = [
			"##__NAME__##",
		];
		$replaceArr = [
			$name,
		];

		$html = file_get_contents(storage_path("email/new_member.html"));
		$html = str_replace($searchArr, $replaceArr, $html);

        try {
            $mail = (new EmailController)->PHPMailerSetUp();
            $mail->Subject = "=?UTF-8?B?".base64_encode("God Plus 神家歡迎你")."?=";
            $mail->Body = $html;
            $mail->AddAddress($email);

		    if( !$mail->send() ) {
                return back()->with("failed", "Email not sent.")->withErrors($mail->ErrorInfo);
            }else {
                return back()->with("success", "Email has been sent.");
            }
 
        } catch (Exception $e) {
             return back()->with('error','Message could not be sent.');
        }

	}

}
