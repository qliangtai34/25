<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrection;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CorrectionController extends Controller
{
    /**
     * 修正申請一覧
     */
    public function index(Request $request)
    {
        // status = pending / approved / rejected
        $status = $request->query('status', 'pending');

        $corrections = AttendanceCorrection::with(['user', 'attendance'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.corrections.index', compact('corrections', 'status'));
    }

    /**
     * 修正申請詳細
     */
    public function show($id)
    {
        $correction = AttendanceCorrection::with(['user', 'attendance'])
            ->findOrFail($id);

        return view('admin.corrections.show', compact('correction'));
    }

    /**
     * 承認処理
     */

private function normalizeTime($time)
{
    if (!$time) return null;

    // DATETIMEなら時刻部分だけ取り出す
    if (str_contains($time, ' ')) {
        $time = explode(' ', $time)[1];
    }

    // マイクロ秒削除
    $time = preg_replace('/\.\d+$/', '', trim($time));

    // HH:MM or HH:MM:SS → HH:MM
    return substr($time, 0, 5);
}




public function approve(AttendanceCorrection $attendance_correct_request)
{
    DB::transaction(function () use ($attendance_correct_request) {

        if ($attendance_correct_request->status !== 'pending') {
            abort(400, 'この申請はすでに処理済みです。');
        }

        $attendance = Attendance::findOrFail(
            $attendance_correct_request->attendance_id
        );

        // 勤務日（Y-m-d）
        $workDate = substr($attendance->date, 0, 10);

        // 1️⃣ 勤怠更新（そのまま）
        $attendance->update([
            'clock_in'  => $attendance_correct_request->new_clock_in,
            'clock_out' => $attendance_correct_request->new_clock_out,
            'note'      => $attendance_correct_request->new_note,
        ]);

        // 2️⃣ 休憩更新
        $attendance->breaks()->delete();

        foreach ($attendance_correct_request->breaks as $break) {

            // 👉 時刻部分だけ文字列で抜く
            $start = substr($break->break_start, 11, 8);
            $end   = substr($break->break_end, 11, 8);

            $attendance->breaks()->create([
                'break_start' => $workDate.' '.$start,
                'break_end'   => $workDate.' '.$end,
            ]);
        }

        $attendance_correct_request->update([
            'status' => 'approved',
        ]);
    });

    return redirect()
        ->route('admin.corrections.index')
        ->with('message', '修正申請を承認しました。');
}







    /**
     * 却下処理
     */
    public function reject($id)
    {
        $correction = AttendanceCorrection::findOrFail($id);

        if ($correction->status !== 'pending') {
            return back()->with('error', 'この申請はすでに処理済みです。');
        }

        $correction->update([
            'status' => 'rejected'
        ]);

        return redirect()->route('admin.corrections.index')
            ->with('message', '修正申請を却下しました。');
    }

    /**
 * 管理者が修正内容を編集する
 */
public function update(Request $request, $id)
{
    $correction = AttendanceCorrection::findOrFail($id);

    if ($correction->status !== 'pending') {
        return back()->with('error', '処理済みの申請は編集できません。');
    }

    $correction->update([
        'new_clock_in'  => $request->new_clock_in,
        'new_clock_out' => $request->new_clock_out,
        'new_breaks'    => $request->new_breaks,
        'new_note'      => $request->new_note,
    ]);

    return back()->with('message', '修正内容を更新しました。');
}

}