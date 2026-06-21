<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use App\Models\AbsenceType;
use App\Models\User;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->get('status');

        $absences = AbsenceRequest::where('user_id', $user->id)
            ->when($status, fn($q) => $q->where('status', $status))
            ->with('absenceType')
            ->latest()
            ->paginate(15);

        return view('absences.index', compact('absences'));
    }

    public function create()
    {
        $user = auth()->user();
        $absenceTypes = AbsenceType::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $colleagues = User::where('organization_id', $user->organization_id)
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('absences.create', compact('absenceTypes', 'colleagues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'absence_type_id' => 'required|exists:absence_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'half_day_start' => 'boolean',
            'half_day_end' => 'boolean',
            'substitute_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        // Calculate total days
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInWeekdays($endDate) + 1;

        if ($request->boolean('half_day_start')) $totalDays -= 0.5;
        if ($request->boolean('half_day_end')) $totalDays -= 0.5;

        AbsenceRequest::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'absence_type_id' => $validated['absence_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'half_day_start' => $request->boolean('half_day_start'),
            'half_day_end' => $request->boolean('half_day_end'),
            'total_days' => $totalDays,
            'status' => 'pending',
            'substitute_id' => $validated['substitute_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('absences.index')
            ->with('success', __('app.success'));
    }

    public function approve(AbsenceRequest $absence)
    {
        $absence->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', __('app.approved'));
    }

    public function reject(Request $request, AbsenceRequest $absence)
    {
        $absence->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason'),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', __('app.rejected'));
    }
}
