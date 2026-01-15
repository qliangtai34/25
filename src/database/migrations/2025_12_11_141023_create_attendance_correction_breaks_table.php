<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_correction_breaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_correction_id');
            $table->dateTime('break_start')->nullable();
            $table->dateTime('break_end')->nullable();
            $table->timestamps();

            $table->foreign('attendance_correction_id')
                  ->references('id')->on('attendance_corrections')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_correction_breaks');
    }
};