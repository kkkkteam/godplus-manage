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
            $table->char('message_type', length: 24);
            $table->text('text')->nullable();
            $table->char('channel', length: 24);
            $table->char('message_uuid', length: 64);
            $table->char('context_status', length: 24)->nullable();
            $table->json('profile')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_webhooks');
    }
};

