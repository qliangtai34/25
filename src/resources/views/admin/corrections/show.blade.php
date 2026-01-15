@extends('layouts.admin')

@section('content')
<h2>修正申請 詳細</h2>

<h4>申請者：{{ $correction->user->name }}</h4>
<h4>日付：{{ \Carbon\Carbon::parse($correction->attendance->date)->format('Y-m-d') }}</h4>

<table class="table">
    <tr>
        <th>項目</th>
        <th>元の値</th>
        <th>修正後</th>
    </tr>

    <tr>
        <td>出勤</td>
        <td>{{ $correction->original_clock_in ? \Carbon\Carbon::parse($correction->original_clock_in)->format('Y-m-d H:i') : '—' }}</td>
        <td>{{ $correction->new_clock_in ? \Carbon\Carbon::parse($correction->new_clock_in)->format('Y-m-d H:i') : '—' }}</td>
    </tr>

    <tr>
        <td>退勤</td>
        <td>{{ $correction->original_clock_out ? \Carbon\Carbon::parse($correction->original_clock_out)->format('Y-m-d H:i') : '—' }}</td>
        <td>{{ $correction->new_clock_out ? \Carbon\Carbon::parse($correction->new_clock_out)->format('Y-m-d H:i') : '—' }}</td>
    </tr>

    <tr>
        <td>休憩</td>
        <td>
            @if($correction->original_breaks && $correction->original_breaks->count())
                <ul class="list-unstyled mb-0">
                    @foreach($correction->original_breaks as $break)
                        <li>
                            開始: {{ \Carbon\Carbon::parse($break->break_start)->format('H:i') ?? '—' }} /
                            終了: {{ \Carbon\Carbon::parse($break->break_end)->format('H:i') ?? '—' }}
                        </li>
                    @endforeach
                </ul>
            @else
                —
            @endif
        </td>
        <td>
            @if($correction->breaks && $correction->breaks->count())
                <ul class="list-unstyled mb-0">
                    @foreach($correction->breaks as $break)
                        <li>
                            開始: {{ \Carbon\Carbon::parse($break->break_start)->format('H:i') ?? '—' }} /
                            終了: {{ \Carbon\Carbon::parse($break->break_end)->format('H:i') ?? '—' }}
                        </li>
                    @endforeach
                </ul>
            @else
                —
            @endif
        </td>
    </tr>

    <tr>
        <td>備考</td>
        <td>{{ $correction->original_note ?? '—' }}</td>
        <td>{{ $correction->new_note ?? '—' }}</td>
    </tr>
</table>

<form method="POST"
      action="{{ route('stamp_correction_request.approve', $correction->id) }}">
    @csrf
    <button class="btn btn-success btn-sm">承認</button>
</form>


<form action="{{ route('admin.corrections.reject', $correction->id) }}" method="POST" class="mt-2">
    @csrf
    <button class="btn btn-danger">却下</button>
</form>

@endsection