<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\AdminAttendanceUpdateRequest;

class AttendanceController extends Controller
{
    public function index($date = null)
{
    // 初期表示は今日
    $targetDate = $date ? Carbon::parse($date) : Carbon::today();

    // targetDate の勤怠のみ取得
    $attendances = Attendance::with(['user', 'breaks'])
    ->whereDate('date', $targetDate)
    ->whereHas('user', function ($q) {
        $q->where('is_admin', false);
    })
    ->orderBy('user_id')
    ->get();



    // 前日/翌日
    $prevDate = $targetDate->copy()->subDay()->format('Y-m-d');
    $nextDate = $targetDate->copy()->addDay()->format('Y-m-d');

    return view('admin.attendances.index', compact(
        'attendances', 'targetDate', 'prevDate', 'nextDate'
    ));
}


    public function showStaffAttendance(Request $request, $id)
{
    $year  = $request->query('year', now()->year);
    $month = $request->query('month', now()->month);

    $user = User::findOrFail($id);

    $attendances = Attendance::with('breaks')
        ->where('user_id', $id)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->orderBy('date')
        ->get();

    return view('admin.attendances.staff', compact(
        'user', 'attendances', 'year', 'month'
    ));
}



public function show($id)
{
    $attendance = Attendance::with('user')->findOrFail($id);

    return view('admin.attendances.show', compact('attendance'));
}

public function update(AdminAttendanceUpdateRequest $request, $id)
{
    $attendance = Attendance::with('breaks')->findOrFail($id);

    $attendance->update([
        'clock_in'  => $request->clock_in,
        'clock_out' => $request->clock_out,
        'note'      => $request->note,
    ]);

    foreach ($request->breaks ?? [] as $breakId => $breakData) {

        if (empty($breakData['break_start']) || empty($breakData['break_end'])) {
            continue;
        }

        AttendanceBreak::where('id', $breakId)
            ->where('attendance_id', $attendance->id)
            ->update([
                'break_start' => $breakData['break_start'],
                'break_end'   => $breakData['break_end'],
            ]);
    }

    return back()->with('success', '勤怠を修正しました。');
}

}