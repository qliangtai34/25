@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>{{ $user->name }} さんの {{ $year }}年{{ $month }}月 の勤怠</h2>

    <table class="table table-bordered mt-3 text-center align-middle">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩開始</th>
                <th>休憩終了</th>
                <th>合計勤務時間</th>
            </tr>
        </thead>

        <tbody>
        @forelse($attendances as $atd)
            <tr>
                {{-- 日付 --}}
                <td>{{ $atd->date }}</td>

                {{-- 出勤 --}}
                <td>{{ optional($atd->clock_in)->format('H:i') ?? '—' }}</td>

                {{-- 退勤 --}}
                <td>{{ optional($atd->clock_out)->format('H:i') ?? '—' }}</td>

                {{-- 休憩開始 --}}
                <td class="text-start">
                    @forelse($atd->breaks as $break)
                        {{ optional($break->break_start)->format('H:i') }}<br>
                    @empty
                        —
                    @endforelse
                </td>

                {{-- 休憩終了 --}}
                <td class="text-start">
                    @forelse($atd->breaks as $break)
                        {{ optional($break->break_end)->format('H:i') }}<br>
                    @empty
                        —
                    @endforelse
                </td>

                {{-- 合計勤務時間 --}}
                <td>
                    {{ $atd->total_work_hours ?? '—' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">データがありません</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <a href="{{ route('admin.attendances') }}" class="btn btn-secondary">
        戻る
    </a>
</div>
@endsection
