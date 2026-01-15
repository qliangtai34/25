<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class AttendanceCorrectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'new_clock_in'  => ['required', 'date'],
            'new_clock_out' => ['required', 'date'],
            'remark'        => ['required'],
            'break_start.*' => ['nullable', 'date'],
            'break_end.*'   => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'remark.required' => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $clockIn  = $this->new_clock_in ? Carbon::parse($this->new_clock_in) : null;
            $clockOut = $this->new_clock_out ? Carbon::parse($this->new_clock_out) : null;

            /*
            | 1. 出勤 > 退勤
            */
            if ($clockIn && $clockOut && $clockIn->gte($clockOut)) {
                $validator->errors()->add(
                    'new_clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            /*
            | 2・3. 休憩時間
            */
            $breakStarts = $this->break_start ?? [];
            $breakEnds   = $this->break_end ?? [];

            foreach ($breakStarts as $i => $start) {

                $startTime = $start ? Carbon::parse($start) : null;
                $endTime   = isset($breakEnds[$i]) && $breakEnds[$i]
                                ? Carbon::parse($breakEnds[$i])
                                : null;

                // 2. 休憩開始が不正
                if ($startTime) {
                    if (
                        ($clockIn && $startTime->lt($clockIn)) ||
                        ($clockOut && $startTime->gt($clockOut))
                    ) {
                        $validator->errors()->add(
                            "break_start.$i",
                            '休憩時間が不適切な値です'
                        );
                    }
                }

                // 3. 休憩終了が退勤後
                if ($endTime && $clockOut && $endTime->gt($clockOut)) {
                    $validator->errors()->add(
                        "break_end.$i",
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
            }
        });
    }
}
