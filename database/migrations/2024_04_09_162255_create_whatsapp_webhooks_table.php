<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // 	//  Global variables
	// public $_tableNameArray = array(
	// 	"whatsapp_webhooks",
	// 	"whatsapp_webhook_archives",
	// );

    public function up(): void
    {
        Schema::create('whatsapp_webhooks', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();

            $table->char('from', length: 24);
            $table->char('to', length: 24);
            $table->char('message_type', length: 24)->nullable();
            $table->char('sms_sid', length: 64)->nullable();
            $table->char('status', length: 24)->nullable();
            $table->text('body')->nullable();
            $table->char('profile_name', length: 64)->nullable();
            $table->json('content');

            $table->timestamps();

        });
    }

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_webhooks');
    }
};

