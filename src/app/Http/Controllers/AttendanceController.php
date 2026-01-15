<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\AttendanceCorrection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceCorrectionRequest;

class AttendanceController extends Controller
{
    // 打刻画面
    public function index()
    {
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'date'    => $today,
            ],
            [
                'status' => '勤務外',
            ]
        );

        return view('attendance.index', compact('attendance'));
    }


    // 出勤
    public function clockIn()
{
    $attendance = Attendance::where('user_id', Auth::id())
        ->whereDate('date', Carbon::today())
        ->firstOrFail();

    // ❌ すでに出勤済み
    if ($attendance->clock_in) {
        return back()->with('error', '本日はすでに出勤しています。');
    }

    $attendance->update([
        'clock_in' => Carbon::now(),
        'status'   => '出勤中',
    ]);

    return back()->with('message', '出勤しました。');
}



    // 休憩開始
    public function breakStart()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())->first();

        if ($attendance->status !== '出勤中') {
            return back()->with('error', '休憩開始できません。');
        }

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start'   => Carbon::now(),
        ]);

        $attendance->update(['status' => '休憩中']);

        return back()->with('message', '休憩を開始しました。');
    }


    // 休憩終了
    public function breakEnd()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())->first();

        $break = AttendanceBreak::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->first();

        if (!$break) {
            return back()->with('error', '休憩中ではありません。');
        }

        $break->update(['break_end' => Carbon::now()]);
        $attendance->update(['status' => '出勤中']);

        return back()->with('message', '休憩を終了しました。');
    }


    // 退勤
    public function clockOut()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())->first();

        if ($attendance->status === '勤務外') {
            return back()->with('error', '出勤していません。');
        }

        $attendance->update([
            'clock_out' => Carbon::now(),
            'status'    => '勤務外',
        ]);

        return back()->with('message', '退勤しました。');
    }


    // 1日の詳細
    public function detail($id)
{
    $attendance = Attendance::with('breaks')
        ->where('id', $id)
        ->where('user_id', Auth::id()) // ← 他人の勤怠を見れないように
        ->firstOrFail();

    $correction = AttendanceCorrection::where('user_id', Auth::id())
        ->where('attendance_id', $attendance->id)
        ->latest()
        ->first();

    return view('attendance.detail', compact('attendance', 'correction'));
}



    // 全体一覧
    public function list($year = null, $month = null)
{
    $year = $year ?? now()->year;
    $month = $month ?? now()->month;

    $start = "{$year}-{$month}-01";
    $end   = date("Y-m-t", strtotime($start));

    $attendances = Attendance::where('user_id', Auth::id())
        ->whereBetween('date', [$start, $end])
        ->with('breaks') // ← これを追加
        ->orderBy('date', 'asc')
        ->get();

    $prevMonthDate = date("Y-m", strtotime("-1 month", strtotime($start)));
    $nextMonthDate = date("Y-m", strtotime("+1 month", strtotime($start)));

    return view('attendance.list', [
        'attendances' => $attendances,
        'year' => $year,
        'month' => $month,
        'prevYear' => explode('-', $prevMonthDate)[0],
        'prevMonth' => explode('-', $prevMonthDate)[1],
        'nextYear' => explode('-', $nextMonthDate)[0],
        'nextMonth' => explode('-', $nextMonthDate)[1],
    ]);
}




 public function requestCorrection(AttendanceCorrectionRequest $request, $id)
{
    $attendance = Attendance::findOrFail($id);

    $correction = AttendanceCorrection::create([
        'user_id'       => auth()->id(),
        'attendance_id' => $attendance->id,
        'new_clock_in'  => $request->new_clock_in,
        'new_clock_out' => $request->new_clock_out,
        'new_note'      => $request->remark,
        'status'        => 'pending',
    ]);

    $breakStarts = $request->input('break_start', []);
    $breakEnds   = $request->input('break_end', []);

    foreach ($breakStarts as $i => $start) {
        $end = $breakEnds[$i] ?? null;

        if ($start || $end) {
            \App\Models\AttendanceCorrectionBreak::create([
                'attendance_correction_id' => $correction->id,
                'break_start' => $start,
                'break_end'   => $end,
            ]);
        }
    }

    return redirect()
        ->route('attendance.detail', $attendance->id)
        ->with('message', '修正申請を送信しました（承認待ち）');
}







public function requestList(Request $request)
{
    // 管理者の場合
    if (auth()->user()->is_admin) {
        $status = $request->query('status', 'pending');

        $corrections = AttendanceCorrection::with(['user', 'attendance'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.corrections.index', compact('corrections', 'status'));
    }

    // 一般ユーザーの場合
    $status = $request->query('status', 'pending');

    $requests = AttendanceCorrection::with('attendance')
        ->where('user_id', auth()->id())
        ->where('status', $status)
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    return view('attendance.requests', compact('requests', 'status'));
}

    
}