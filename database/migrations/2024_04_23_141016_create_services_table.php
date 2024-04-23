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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();

            $table->char('slug', length: 24);
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->char('title', length: 64);
            $table->char('speaker', length: 24);
            $table->text('registration_command')->nullable();
            $table->text('attendance_command')->nullable();
            $table->boolean('command_valid')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
