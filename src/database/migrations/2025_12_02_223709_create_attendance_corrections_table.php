<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            
            // どのユーザーの勤怠か
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // どの勤怠（attendance_id）の修正か
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');

            // 出勤・退勤の修正申請時刻
            $table->timestamp('new_clock_in')->nullable();
            $table->timestamp('new_clock_out')->nullable();

            // 修正申請全体の備考
            $table->text('new_note')->nullable();

            // 修正ステータス: pending / approved / rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_corrections');
    }
};