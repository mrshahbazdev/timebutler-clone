<?php

namespace App\Http\Controllers;

use App\Exports\AbsenceReportExport;
use App\Exports\TimeTrackingReportExport;
use App\Models\AbsenceRequest;
use App\Models\TimeEntry;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function absences(Request $request)
    {
        $user = $request->user();
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        $absences = AbsenceRequest::where('organization_id', $user->organization_id)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->with(['user', 'absenceType'])
            ->orderBy('start_date')
            ->get();

        $summary = $absences->groupBy(fn($a) => $a->absenceType->name ?? 'Unknown')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total_days' => $group->sum('total_days'),
            ]);

        return view('reports.absences', compact('absences', 'summary', 'startDate', 'endDate'));
    }

    public function absencesPdf(Request $request)
    {
        $user = $request->user();
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        $absences = AbsenceRequest::where('organization_id', $user->organization_id)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->with(['user', 'absenceType'])
            ->orderBy('start_date')
            ->get();

        $summary = $absences->groupBy(fn($a) => $a->absenceType->name ?? 'Unknown')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total_days' => $group->sum('total_days'),
            ]);

        $orgName = $user->organization->name ?? 'Organization';

        $pdf = Pdf::loadView('reports.pdf.absences', compact('absences', 'summary', 'startDate', 'endDate', 'orgName'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("absences-report-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.pdf");
    }

    public function absencesExcel(Request $request)
    {
        $user = $request->user();
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        return Excel::download(
            new AbsenceReportExport($user->organization_id, $startDate, $endDate),
            "absences-report-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.xlsx"
        );
    }

    public function timeTracking(Request $request)
    {
        $user = $request->user();
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));
        $employeeId = $request->get('employee_id');

        $query = TimeEntry::where('organization_id', $user->organization_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('user');

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        $entries = $query->orderBy('date')->orderBy('user_id')->get();

        $employees = User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $totalMinutes = $entries->sum('total_minutes');
        $totalHours = intdiv($totalMinutes, 60);
        $remainingMinutes = $totalMinutes % 60;

        return view('reports.time-tracking', compact('entries', 'employees', 'startDate', 'endDate', 'employeeId', 'totalHours', 'remainingMinutes'));
    }

    public function timeTrackingPdf(Request $request)
    {
        $user = $request->user();
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));
        $employeeId = $request->get('employee_id');

        $query = TimeEntry::where('organization_id', $user->organization_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('user');

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        $entries = $query->orderBy('date')->orderBy('user_id')->get();
        $orgName = $user->organization->name ?? 'Organization';
        $totalMinutes = $entries->sum('total_minutes');

        $pdf = Pdf::loadView('reports.pdf.time-tracking', compact('entries', 'startDate', 'endDate', 'orgName', 'totalMinutes'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("time-tracking-report-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.pdf");
    }

    public function timeTrackingExcel(Request $request)
    {
        $user = $request->user();
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));
        $employeeId = $request->get('employee_id');

        return Excel::download(
            new TimeTrackingReportExport($user->organization_id, $startDate, $endDate, $employeeId),
            "time-tracking-report-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.xlsx"
        );
    }
}
