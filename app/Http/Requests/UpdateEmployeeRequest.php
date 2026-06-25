<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($employee->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'employee_number' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'weekly_hours' => 'required|numeric|min:0|max:60',
            'vacation_days_per_year' => 'required|integer|min:0|max:60',
            'employment_start' => 'nullable|date',
            'employment_end' => 'nullable|date|after:employment_start',
            'role' => 'required|exists:roles,name',
            'locale' => 'required|in:en,de',
            'is_active' => 'boolean',
        ];
    }
}
