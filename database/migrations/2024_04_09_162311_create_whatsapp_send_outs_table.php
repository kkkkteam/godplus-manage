<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_send_outs', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            
            $table->char('from', length: 24);
            $table->char('to', length: 24);
            $table->text('body')->nullable();
            $table->json('content');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_send_outs');
    }
};
