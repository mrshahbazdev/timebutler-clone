<?php

namespace App\Exports;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimeTrackingReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected int $organizationId,
        protected Carbon $startDate,
        protected Carbon $endDate,
        protected ?int $employeeId = null,
    ) {}

    public function collection()
    {
        $query = TimeEntry::where('organization_id', $this->organizationId)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->with('user');

        if ($this->employeeId) {
            $query->where('user_id', $this->employeeId);
        }

        return $query->orderBy('date')->orderBy('user_id')->get();
    }

    public function headings(): array
    {
        return [
            __('app.employee_name'),
            __('app.start_date'),
            __('app.start_time'),
            __('app.end_time'),
            __('app.break') . ' (min)',
            __('app.total_hours'),
            __('app.project'),
            __('app.status'),
            __('app.notes'),
        ];
    }

    public function map($entry): array
    {
        return [
            $entry->user->name ?? '-',
            $entry->date->format('d.m.Y'),
            $entry->start_time ?? '-',
            $entry->end_time ?? '-',
            $entry->break_minutes,
            $entry->formatted_hours,
            $entry->project ?? '-',
            __('app.' . $entry->status),
            $entry->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
