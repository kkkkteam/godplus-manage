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
        Schema::create('service_attendances', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();

            $table->char('name', length: 24);
            $table->char('register_id', length: 8)->nullable();
            $table->char('mobile', length: 24);
            $table->char('service_slug', length: 24);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_attendances');
    }
};
