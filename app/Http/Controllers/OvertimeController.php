<?php

namespace App\Http\Controllers;

use App\Models\OvertimeBalance;
use App\Models\User;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $balances = OvertimeBalance::where('user_id', $user->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $totalMinutes = $balances->sum('balance_minutes');

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $thisMonthBalance = $balances
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();

        return view('overtime.index', compact('balances', 'totalMinutes', 'thisMonthBalance'));
    }

    public function admin(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $employees = User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedUserId = $request->input('employee_id');
        $selectedYear = $request->input('year', now()->year);
        $balances = collect();

        if ($selectedUserId) {
            $balances = OvertimeBalance::where('user_id', $selectedUserId)
                ->where('year', $selectedYear)
                ->orderBy('month')
                ->get()
                ->keyBy('month');
        }

        return view('overtime.admin', compact('employees', 'balances', 'selectedUserId', 'selectedYear'));
    }

    public function storeAdjustment(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020|max:2099',
            'month' => 'required|integer|min:1|max:12',
            'hours' => 'required|integer',
            'minutes' => 'required|integer|min:0|max:59',
            'is_negative' => 'nullable',
        ]);

        $employee = User::where('id', $validated['employee_id'])
            ->where('organization_id', $user->organization_id)
            ->firstOrFail();

        $totalMinutes = ($validated['hours'] * 60) + $validated['minutes'];
        if ($request->has('is_negative')) {
            $totalMinutes = -$totalMinutes;
        }

        OvertimeBalance::updateOrCreate(
            [
                'user_id' => $employee->id,
                'year' => $validated['year'],
                'month' => $validated['month'],
            ],
            [
                'organization_id' => $user->organization_id,
                'balance_minutes' => $totalMinutes,
            ]
        );

        return redirect()->route('overtime.admin', [
            'employee_id' => $employee->id,
            'year' => $validated['year'],
        ])->with('success', app()->getLocale() === 'de' ? 'Überstunden gespeichert.' : 'Overtime saved.');
    }

    public function bulkStore(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020|max:2099',
            'entries' => 'required|array',
            'entries.*.month' => 'required|integer|min:1|max:12',
            'entries.*.hours' => 'required|integer',
            'entries.*.minutes' => 'required|integer|min:0|max:59',
            'entries.*.is_negative' => 'nullable',
        ]);

        $employee = User::where('id', $validated['employee_id'])
            ->where('organization_id', $user->organization_id)
            ->firstOrFail();

        foreach ($validated['entries'] as $entry) {
            $totalMinutes = ($entry['hours'] * 60) + $entry['minutes'];
            if (!empty($entry['is_negative'])) {
                $totalMinutes = -$totalMinutes;
            }

            OvertimeBalance::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'year' => $validated['year'],
                    'month' => $entry['month'],
                ],
                [
                    'organization_id' => $user->organization_id,
                    'balance_minutes' => $totalMinutes,
                ]
            );
        }

        return redirect()->route('overtime.admin', [
            'employee_id' => $employee->id,
            'year' => $validated['year'],
        ])->with('success', app()->getLocale() === 'de' ? 'Überstunden für alle Monate gespeichert.' : 'Overtime saved for all months.');
    }
}
