@extends('layouts.app')

@section('title', '勤怠一覧')

@section('content')

<h1>勤怠一覧 ({{ $year }}年 {{ $month }}月)</h1>

<div style="margin-bottom: 20px;">
    <a href="{{ route('attendance.list.month', ['year' => $prevYear, 'month' => $prevMonth]) }}">← 前月</a>
    |
    <a href="{{ route('attendance.list.month', ['year' => $nextYear, 'month' => $nextMonth]) }}">翌月 →</a>
</div>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>日付</th>
            <th>出勤時刻</th>
            <th>退勤時刻</th>
            <th>休憩回数</th>
            <th>休憩詳細</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $attendance)
        <tr>
            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') }}</td>

            <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '—' }}</td>
<td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '—' }}</td>


            {{-- 休憩回数 --}}
            <td>{{ $attendance->breaks->count() }}</td>

            {{-- 休憩詳細 --}}
            <td>
                @forelse($attendance->breaks as $break)
                    開始: {{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '—' }}
終了: {{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '—' }}
<br>
                @empty
                    —
                @endforelse
            </td>

            <td>
                <a href="{{ route('attendance.detail', $attendance->id) }}">
    詳細
</a>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection