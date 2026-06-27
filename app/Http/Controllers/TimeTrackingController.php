<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Services\OvertimeCalculator;
use Illuminate\Http\Request;

class TimeTrackingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $entries = TimeEntry::where('user_id', $user->id)
            ->orderByDesc('date')
            ->paginate(15);

        $todayEntries = TimeEntry::where('user_id', $user->id)
            ->where('date', today())
            ->orderByDesc('start_time')
            ->get();

        $todayEntry = $todayEntries->firstWhere('status', 'draft') ?? $todayEntries->first();
        $totalMinutesToday = $todayEntries->sum('total_minutes');

        return view('time-tracking.index', compact('entries', 'todayEntry', 'todayEntries', 'totalMinutesToday'));
    }

    public function team(Request $request)
    {
        $user = $request->user();
        
        if (!$user->can('manage_employees') && !$user->can('manage_overtime') && !$user->can('view_team_absences') && !$user->hasRole('admin')) {
            abort(403);
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');

        $query = TimeEntry::where('organization_id', $user->organization_id)
            ->whereBetween('date', [\Carbon\Carbon::parse($startDate), \Carbon\Carbon::parse($endDate)])
            ->with('user');

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        $entries = $query->orderByDesc('date')
            ->orderBy('user_id')
            ->paginate(15)
            ->withQueryString();

        $employees = \App\Models\User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('time-tracking.team', compact('entries', 'employees', 'startDate', 'endDate', 'employeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'break_minutes' => 'nullable|integer|min:0',
            'project' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        $startTime = \Carbon\Carbon::parse($validated['start_time']);
        $endTime = \Carbon\Carbon::parse($validated['end_time']);
        $breakMinutes = $validated['break_minutes'] ?? 0;
        $totalMinutes = $startTime->diffInMinutes($endTime) - $breakMinutes;

        TimeEntry::create([
            'user_id' => $user->id,
            'date' => $validated['date'],
            'organization_id' => $user->organization_id,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'break_minutes' => $breakMinutes,
            'total_minutes' => max(0, $totalMinutes),
            'project' => $validated['project'] ?? null,
            'category' => $validated['category'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'submitted',
        ]);

        $calculator = app(OvertimeCalculator::class);
        $calculator->calculateForMonth($user, \Carbon\Carbon::parse($validated['date'])->year, \Carbon\Carbon::parse($validated['date'])->month);

        return redirect()->route('time-tracking.index')
            ->with('success', __('app.success'));
    }

    public function clockIn(Request $request)
    {
        $user = $request->user();

        $existingDraft = TimeEntry::where('user_id', $user->id)
            ->where('date', today())
            ->where('status', 'draft')
            ->first();

        if ($existingDraft) {
            return redirect()->route('time-tracking.index')
                ->with('error', __('app.already_clocked_in') ?? 'Already clocked in');
        }

        TimeEntry::create([
            'user_id' => $user->id,
            'date' => today(),
            'organization_id' => $user->organization_id,
            'start_time' => now()->format('H:i'),
            'status' => 'draft',
        ]);

        return redirect()->route('time-tracking.index')
            ->with('success', __('app.clock_in') . ' - ' . now()->format('H:i'));
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'break_minutes' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();
        $entry = TimeEntry::where('user_id', $user->id)
            ->where('date', today())
            ->where('status', 'draft')
            ->first();

        if ($entry) {
            $startTime = \Carbon\Carbon::parse($entry->start_time);
            $endTime = now();
            $breakMinutes = $request->input('break_minutes', 0);
            $totalMinutes = $startTime->diffInMinutes($endTime) - $breakMinutes;

            $entry->update([
                'end_time' => $endTime->format('H:i'),
                'break_minutes' => $breakMinutes,
                'total_minutes' => max(0, $totalMinutes),
                'status' => 'submitted',
            ]);

            $calculator = app(OvertimeCalculator::class);
            $calculator->calculateForMonth($user, today()->year, today()->month);
        }

        return redirect()->route('time-tracking.index')
            ->with('success', __('app.clock_out') . ' - ' . now()->format('H:i'));
    }

    public function edit(Request $request, TimeEntry $time_tracking)
    {
        $user = $request->user();
        if ($time_tracking->user_id !== $user->id) {
            abort(403);
        }

        $entries = TimeEntry::where('user_id', $user->id)
            ->orderByDesc('date')
            ->paginate(15);

        $todayEntries = TimeEntry::where('user_id', $user->id)
            ->where('date', today())
            ->orderByDesc('start_time')
            ->get();

        $todayEntry = $todayEntries->firstWhere('status', 'draft') ?? $todayEntries->first();
        $totalMinutesToday = $todayEntries->sum('total_minutes');

        return view('time-tracking.index', compact('entries', 'todayEntry', 'todayEntries', 'totalMinutesToday'))
            ->with('editEntry', $time_tracking);
    }

    public function update(Request $request, TimeEntry $time_tracking)
    {
        $user = $request->user();
        if ($time_tracking->user_id !== $user->id) {
            abort(403);
        }

        if ($time_tracking->status === 'approved') {
            return redirect()->route('time-tracking.index')
                ->with('error', __('app.error'));
        }

        $validated = $request->validate([
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'break_minutes' => 'nullable|integer|min:0',
            'project' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $startTime = \Carbon\Carbon::parse($validated['start_time']);
        $endTime = \Carbon\Carbon::parse($validated['end_time']);
        $breakMinutes = $validated['break_minutes'] ?? 0;
        $totalMinutes = $startTime->diffInMinutes($endTime) - $breakMinutes;

        $time_tracking->update([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'break_minutes' => $breakMinutes,
            'total_minutes' => max(0, $totalMinutes),
            'project' => $validated['project'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $calculator = app(OvertimeCalculator::class);
        $calculator->calculateForMonth($user, $time_tracking->date->year, $time_tracking->date->month);

        return redirect()->route('time-tracking.index')
            ->with('success', __('app.success'));
    }
}
