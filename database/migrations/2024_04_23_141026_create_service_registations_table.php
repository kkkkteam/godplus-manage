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
        Schema::create('service_registations', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();

            $table->char('name', length: 24);
            $table->char('mobile', length: 24)->nullable();
            
            //newcomer
            $table->char('recommend_by_name', length: 24)->nullable();
            $table->char('recommend_by_mobile', length: 24)->nullable();
            $table->char('age_range')->nullable();
            $table->boolean('is_newcomer')->default(false);

            $table->char('service_slug', length: 24);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_registations');
    }
};
