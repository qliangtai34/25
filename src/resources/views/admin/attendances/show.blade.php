@extends('layouts.admin')

@section('content')
<h2>勤怠詳細（{{ $attendance->date }}）</h2>

<p>ユーザー：{{ $attendance->user->name }}</p>

{{-- 成功メッセージ --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- 🔴 バリデーションエラー表示（全体） --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
    @csrf

    {{-- 出勤 --}}
    <div class="mb-3">
        <label>出勤</label>
        <input type="datetime-local"
               name="clock_in"
               class="form-control @error('clock_in') is-invalid @enderror"
               value="{{ old('clock_in', optional($attendance->clock_in)->format('Y-m-d\TH:i')) }}">

        @error('clock_in')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- 退勤 --}}
    <div class="mb-3">
        <label>退勤</label>
        <input type="datetime-local"
               name="clock_out"
               class="form-control @error('clock_out') is-invalid @enderror"
               value="{{ old('clock_out', optional($attendance->clock_out)->format('Y-m-d\TH:i')) }}">

        @error('clock_out')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <hr>

    <h4>休憩</h4>

    @foreach($attendance->breaks as $break)
        <div class="border p-3 mb-2">

            {{-- 休憩開始 --}}
            <div class="mb-2">
                <label>休憩開始</label>
                <input type="datetime-local"
                       name="breaks[{{ $break->id }}][break_start]"
                       class="form-control @error("breaks.{$break->id}.break_start") is-invalid @enderror"
                       value="{{ old("breaks.{$break->id}.break_start", optional($break->break_start)->format('Y-m-d\TH:i')) }}">

                @error("breaks.{$break->id}.break_start")
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- 休憩終了 --}}
            <div>
                <label>休憩終了</label>
                <input type="datetime-local"
                       name="breaks[{{ $break->id }}][break_end]"
                       class="form-control @error("breaks.{$break->id}.break_end") is-invalid @enderror"
                       value="{{ old("breaks.{$break->id}.break_end", optional($break->break_end)->format('Y-m-d\TH:i')) }}">

                @error("breaks.{$break->id}.break_end")
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>
    @endforeach

    <hr>

    {{-- 備考 --}}
    <div class="mb-3">
        <label>備考</label>
        <textarea name="note"
                  class="form-control @error('note') is-invalid @enderror">{{ old('note', $attendance->note) }}</textarea>

        @error('note')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button class="btn btn-primary">修正する</button>
</form>
@endsection
