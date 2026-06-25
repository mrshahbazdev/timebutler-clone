<?php

namespace App\Http\Requests;

use App\Models\AbsenceRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'absence_type_id' => 'required|exists:absence_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'half_day_start' => 'boolean',
            'half_day_end' => 'boolean',
            'substitute_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'request_type' => 'in:request,blocked',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            if (!$this->start_date || !$this->end_date) return;

            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);

            $overlap = AbsenceRequest::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q2) use ($startDate, $endDate) {
                          $q2->where('start_date', '<=', $startDate)
                             ->where('end_date', '>=', $endDate);
                      });
                })
                ->exists();

            if ($overlap) {
                $validator->errors()->add('start_date', __('app.absence_overlap_error'));
            }
        });
    }
}
