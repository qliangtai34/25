<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdminAttendanceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true; // 管理者チェックするならここで
    }

    public function rules()
    {
        return [
            'clock_in'  => ['required', 'date'],
            'clock_out' => ['required', 'date'],
            'note'      => ['required'],
            'breaks.*.break_start' => ['nullable', 'date'],
            'breaks.*.break_end'   => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $clockIn  = $this->clock_in;
            $clockOut = $this->clock_out;

            /*
            |----------------------------------
            | 1. 出勤 > 退勤
            |----------------------------------
            */
            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add(
                    'clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            /*
            |----------------------------------
            | 2・3. 休憩チェック
            |----------------------------------
            */
            foreach ($this->breaks ?? [] as $break) {

                $start = $break['break_start'] ?? null;
                $end   = $break['break_end'] ?? null;

                // ② 休憩開始が不正
                if ($start) {
                    if (
                        ($clockIn && $start < $clockIn) ||
                        ($clockOut && $start > $clockOut)
                    ) {
                        $validator->errors()->add(
                            'breaks',
                            '休憩時間が不適切な値です'
                        );
                    }
                }

                // ③ 休憩終了が退勤後
                if ($end && $clockOut && $end > $clockOut) {
                    $validator->errors()->add(
                        'breaks',
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
            }
        });
    }
}
