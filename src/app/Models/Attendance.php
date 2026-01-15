<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'note',
        'status',
    ];

    protected $dates = [
        'clock_in',
        'clock_out',
        'date',
    ];

    protected $casts = [
        'date'      => 'date',
        'clock_in'  => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function breaks()
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    public function corrections()
{
    return $this->hasMany(AttendanceCorrection::class);
}

public function user()
    {
        return $this->belongsTo(User::class);
    }
}