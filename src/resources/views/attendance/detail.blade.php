@extends('layouts.app')

@section('content')
<div class="container">

    <h2>勤怠詳細（{{ $attendance->date->format('Y-m-d') }}）</h2>

    {{-- メッセージ --}}
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>出勤</th>
            <td>{{ $attendance->clock_in ?? '—' }}</td>
        </tr>
        <tr>
            <th>退勤</th>
            <td>{{ $attendance->clock_out ?? '—' }}</td>
        </tr>
        <tr>
            <th>休憩</th>
            <td>
                @if($attendance->breaks->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($attendance->breaks as $break)
                            <li>
                                開始: {{ $break->break_start ?? '—' }} / 
                                終了: {{ $break->break_end ?? '—' }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <th>備考</th>
            <td>{{ $attendance->note ?? '—' }}</td>
        </tr>
    </table>

    <h3>修正申請</h3>

    {{-- 承認待ちならフォーム非表示 --}}
    @if(isset($correction) && $correction && $correction->status === 'pending')
        <div class="alert alert-warning">
            承認待ちのため修正はできません。
        </div>
    @else
        <form action="{{ route('attendance.requestCorrection', $attendance->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>出勤（修正後）</label>
                <input type="datetime-local" name="new_clock_in" class="form-control"
                    value="{{ optional($attendance->clock_in)->format('Y-m-d\TH:i') }}">
            </div>

            <div class="mb-3">
                <label>退勤（修正後）</label>
                <input type="datetime-local" name="new_clock_out" class="form-control"
                    value="{{ optional($attendance->clock_out)->format('Y-m-d\TH:i') }}">
            </div>

            <h5>休憩（修正後）</h5>
            <div id="break-container">
                {{-- 元の休憩をフォームに表示 --}}
                @foreach($attendance->breaks as $i => $break)
                    <div class="mb-2 break-row">
                        <input type="datetime-local" name="break_start[]" class="form-control mb-1"
                               value="{{ optional($break->break_start)->format('Y-m-d\TH:i') }}">
                        <input type="datetime-local" name="break_end[]" class="form-control"
                               value="{{ optional($break->break_end)->format('Y-m-d\TH:i') }}">
                    </div>
                @endforeach
                {{-- 空の休憩行を1つ追加 --}}
                <div class="mb-2 break-row">
                    <input type="datetime-local" name="break_start[]" class="form-control mb-1">
                    <input type="datetime-local" name="break_end[]" class="form-control">
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="add-break">休憩を追加</button>

            <div class="mb-3">
                <label>備考（修正後）</label>
                <textarea name="new_note" class="form-control">{{ $attendance->note ?? '' }}</textarea>
            </div>

            <button class="btn btn-primary">修正申請</button>
        </form>
    @endif

    <div class="mt-4">
        <a href="{{ route('attendance.list') }}" class="btn btn-secondary">勤怠一覧へ戻る</a>
    </div>

</div>

<script>
document.getElementById('add-break').addEventListener('click', function() {
    const container = document.getElementById('break-container');
    const row = document.createElement('div');
    row.classList.add('mb-2', 'break-row');
    row.innerHTML = `
        <input type="datetime-local" name="break_start[]" class="form-control mb-1">
        <input type="datetime-local" name="break_end[]" class="form-control">
    `;
    container.appendChild(row);
});
</script>
@endsection