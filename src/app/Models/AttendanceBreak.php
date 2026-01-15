<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'correction_id',
        'break_start',
        'break_end',
    ];

    // 追加: 日時キャスト
    protected $casts = [
        'break_start' => 'datetime',
        'break_end' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function correction()
    {
        return $this->belongsTo(AttendanceCorrection::class, 'correction_id');
    }


}