@extends('layouts.admin')

@section('content')
<h2>勤怠詳細（{{ $attendance->date }}）</h2>

<p>ユーザー：{{ $attendance->user->name }}</p>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
    @csrf

    {{-- 出勤 --}}
    <div class="mb-3">
        <label>出勤</label>
        <input type="datetime-local" name="clock_in" class="form-control"
            value="{{ optional($attendance->clock_in)->format('Y-m-d\TH:i') }}">
    </div>

    {{-- 退勤 --}}
    <div class="mb-3">
        <label>退勤</label>
        <input type="datetime-local" name="clock_out" class="form-control"
            value="{{ optional($attendance->clock_out)->format('Y-m-d\TH:i') }}">
    </div>

    <hr>

    <h4>休憩</h4>

    @foreach($attendance->breaks as $break)
        <div class="border p-3 mb-2">
            <div class="mb-2">
                <label>休憩開始</label>
                <input type="datetime-local"
                    name="breaks[{{ $break->id }}][break_start]"
                    class="form-control"
                    value="{{ optional($break->break_start)->format('Y-m-d\TH:i') }}">
            </div>

            <div>
                <label>休憩終了</label>
                <input type="datetime-local"
                    name="breaks[{{ $break->id }}][break_end]"
                    class="form-control"
                    value="{{ optional($break->break_end)->format('Y-m-d\TH:i') }}">
            </div>
        </div>
    @endforeach

    <hr>

    {{-- 備考 --}}
    <div class="mb-3">
        <label>備考</label>
        <textarea name="note" class="form-control">{{ $attendance->note }}</textarea>
    </div>

    <button class="btn btn-primary">修正する</button>
</form>
@endsection