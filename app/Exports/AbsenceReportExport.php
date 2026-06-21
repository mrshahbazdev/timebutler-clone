<?php

namespace App\Exports;

use App\Models\AbsenceRequest;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsenceReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected int $organizationId,
        protected Carbon $startDate,
        protected Carbon $endDate,
    ) {}

    public function collection()
    {
        return AbsenceRequest::where('organization_id', $this->organizationId)
            ->whereBetween('start_date', [$this->startDate, $this->endDate])
            ->with(['user', 'absenceType', 'approver'])
            ->orderBy('start_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            __('app.employee_name'),
            __('app.absence_type'),
            __('app.start_date'),
            __('app.end_date'),
            __('app.days'),
            __('app.status'),
            __('app.notes'),
        ];
    }

    public function map($absence): array
    {
        return [
            $absence->user->name ?? '-',
            $absence->absenceType->name ?? '-',
            $absence->start_date->format('d.m.Y'),
            $absence->end_date->format('d.m.Y'),
            $absence->total_days,
            __('app.' . $absence->status),
            $absence->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
