<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'new_clock_in',
        'new_clock_out',
        'new_note',
        'status',
    ];

    protected $casts = [
        'new_clock_in' => 'datetime',
        'new_clock_out' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
{
    return $this->hasMany(AttendanceCorrectionBreak::class, 'attendance_correction_id');
}

}