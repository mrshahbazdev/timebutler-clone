<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Services\GermanHolidayService;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function __construct(
        protected GermanHolidayService $holidayService,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $year = (int) $request->get('year', now()->year);
        $state = $request->get('state', $user->organization->federal_state ?? 'NW');

        $holidays = Holiday::where('organization_id', $user->organization_id)
            ->where('year', $year)
            ->orderBy('date')
            ->get()
            ->groupBy('type');

        $states = GermanHolidayService::FEDERAL_STATES;

        return view('holidays.index', compact('holidays', 'year', 'state', 'states'));
    }

    public function import(Request $request)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'year' => 'required|integer|min:2024|max:2030',
            'state' => 'required|string|in:' . implode(',', array_keys(GermanHolidayService::FEDERAL_STATES)),
            'import_public_holidays' => 'boolean',
            'import_school_breaks' => 'boolean',
            'import_weekends' => 'boolean',
        ]);

        $user = $request->user();
        $orgId = $user->organization_id;
        $year = $validated['year'];
        $state = $validated['state'];
        $totalImported = 0;

        if ($request->boolean('import_public_holidays', true)) {
            $totalImported += $this->holidayService->importPublicHolidays($orgId, $state, $year);
        }

        if ($request->boolean('import_school_breaks', true)) {
            $totalImported += $this->holidayService->importSchoolBreaks($orgId, $state, $year);
        }

        if ($request->boolean('import_weekends', false)) {
            $totalImported += $this->holidayService->importWeekends($orgId, $year);
        }

        $user->organization->update(['federal_state' => $state]);

        return redirect()->route('holidays.index', ['year' => $year, 'state' => $state])
            ->with('success', __('app.holidays_imported', ['count' => $totalImported]));
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $year = $request->get('year', now()->year);

        Holiday::where('organization_id', $user->organization_id)
            ->where('year', $year)
            ->delete();

        return redirect()->route('holidays.index', ['year' => $year])
            ->with('success', __('app.holidays_deleted'));
    }
}
