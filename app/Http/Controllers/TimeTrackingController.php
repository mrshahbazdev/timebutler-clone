<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use Illuminate\Http\Request;

class TimeTrackingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $entries = TimeEntry::where('user_id', $user->id)
            ->orderByDesc('date')
            ->paginate(15);

        $todayEntry = TimeEntry::where('user_id', $user->id)
            ->where('date', today())
            ->first();

        return view('time-tracking.index', compact('entries', 'todayEntry'));
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

        TimeEntry::updateOrCreate(
            ['user_id' => $user->id, 'date' => $validated['date']],
            [
                'organization_id' => $user->organization_id,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'break_minutes' => $breakMinutes,
                'total_minutes' => max(0, $totalMinutes),
                'project' => $validated['project'] ?? null,
                'category' => $validated['category'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
            ]
        );

        return redirect()->route('time-tracking.index')
            ->with('success', __('app.success'));
    }

    public function clockIn(Request $request)
    {
        $user = $request->user();

        TimeEntry::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            [
                'organization_id' => $user->organization_id,
                'start_time' => now()->format('H:i'),
                'status' => 'draft',
            ]
        );

        return redirect()->route('time-tracking.index')
            ->with('success', __('app.clock_in') . ' - ' . now()->format('H:i'));
    }

    public function clockOut(Request $request)
    {
        $user = $request->user();
        $entry = TimeEntry::where('user_id', $user->id)
            ->where('date', today())
            ->first();

        if ($entry) {
            $startTime = \Carbon\Carbon::parse($entry->start_time);
            $endTime = now();
            $totalMinutes = $startTime->diffInMinutes($endTime) - ($entry->break_minutes ?? 0);

            $entry->update([
                'end_time' => $endTime->format('H:i'),
                'total_minutes' => max(0, $totalMinutes),
            ]);
        }

        return redirect()->route('time-tracking.index')
            ->with('success', __('app.clock_out') . ' - ' . now()->format('H:i'));
    }
}
