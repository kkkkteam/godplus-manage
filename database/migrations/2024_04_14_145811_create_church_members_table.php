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
        Schema::create('church_members', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();

            $table->char('slug', length: 24);
            $table->char('surname_en', length: 64)->nullable();
            $table->char('lastname_en', length: 48)->nullable();
            $table->char('surname_zh', length: 8)->nullable();
            $table->char('lastname_zh', length: 8)->nullable();
            $table->char('nickname', length: 24)->nullable();
            $table->char('mobile', length: 24);
            $table->date('birthday')->nullable();
            $table->date('baptized_at')->nullable();
            $table->char('gender', length: 8)->nullable();
            $table->json('academic')->nullable();
            $table->json('relation')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('church_members');
    }
};
