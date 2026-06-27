<?php

namespace App\Http\Controllers;

use App\Models\OvertimeBalance;
use App\Models\User;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    public function index(Request $request, \App\Services\OvertimeCalculator $calculator)
    {
        $user = $request->user();

        // Ensure current month is up to date
        $calculator->calculateForMonth($user, now()->year, now()->month);

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
        if (!$user->can('manage_overtime')) {
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
            $employee = User::find($selectedUserId);
            if ($employee) {
                // Ensure all months of the selected year up to current month are calculated
                $currentMonth = $selectedYear == now()->year ? now()->month : 12;
                $calculator = app(\App\Services\OvertimeCalculator::class);
                for ($m = 1; $m <= $currentMonth; $m++) {
                    $calculator->calculateForMonth($employee, $selectedYear, $m);
                }
            }

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
        if (!$user->can('manage_overtime')) {
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

        $balance = OvertimeBalance::firstOrNew([
            'user_id' => $employee->id,
            'year' => $validated['year'],
            'month' => $validated['month'],
        ]);

        $balance->organization_id = $user->organization_id;
        $balance->adjustment_minutes = $totalMinutes;
        $balance->balance_minutes = $balance->calculated_minutes + $balance->adjustment_minutes;
        $balance->save();

        return redirect()->route('overtime.admin', [
            'employee_id' => $employee->id,
            'year' => $validated['year'],
        ])->with('success', app()->getLocale() === 'de' ? 'Überstunden gespeichert.' : 'Overtime saved.');
    }

    public function bulkStore(Request $request)
    {
        $user = $request->user();
        if (!$user->can('manage_overtime')) {
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

            $balance = OvertimeBalance::firstOrNew([
                'user_id' => $employee->id,
                'year' => $validated['year'],
                'month' => $entry['month'],
            ]);

            $balance->organization_id = $user->organization_id;
            $balance->adjustment_minutes = $totalMinutes;
            $balance->balance_minutes = $balance->calculated_minutes + $balance->adjustment_minutes;
            $balance->save();
        }

        return redirect()->route('overtime.admin', [
            'employee_id' => $employee->id,
            'year' => $validated['year'],
        ])->with('success', app()->getLocale() === 'de' ? 'Überstunden für alle Monate gespeichert.' : 'Overtime saved for all months.');
    }
}
