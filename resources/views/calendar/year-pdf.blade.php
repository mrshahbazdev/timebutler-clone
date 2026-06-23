<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $orgName }} - {{ __('app.team_calendar') }} {{ $year }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #1f2937; margin: 8px; }
        .header { border-bottom: 2px solid #3b82f6; padding-bottom: 6px; margin-bottom: 10px; }
        .header h1 { font-size: 14px; margin: 0; color: #1e3a5f; }
        .header p { margin: 2px 0 0; color: #6b7280; font-size: 8px; }
        .month-block { margin-bottom: 12px; page-break-inside: avoid; }
        .month-title { font-size: 10px; font-weight: bold; color: #1e3a5f; margin-bottom: 3px; padding: 3px 6px; background: #eff6ff; border-left: 3px solid #3b82f6; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; padding: 2px 1px; text-align: center; font-size: 6px; border: 1px solid #d1d5db; }
        th.name-header { text-align: left; padding-left: 4px; min-width: 90px; font-size: 7px; }
        td { padding: 1px 0; text-align: center; border: 1px solid #e5e7eb; height: 14px; font-size: 6px; }
        td.name-cell { text-align: left; padding-left: 4px; font-weight: bold; font-size: 7px; white-space: nowrap; overflow: hidden; }
        .weekend { background: #f3f4f6; }
        .holiday-cell { background: #fee2e2; color: #dc2626; font-weight: bold; }
        .school-break-cell { background: #fef9c3; }
        .legend { margin-top: 10px; padding: 6px; border: 1px solid #e5e7eb; border-radius: 4px; }
        .legend h3 { font-size: 8px; font-weight: bold; margin: 0 0 4px; }
        .legend-grid { display: flex; flex-wrap: wrap; }
        .legend-item { display: inline-block; margin-right: 12px; margin-bottom: 3px; font-size: 7px; }
        .legend-color { display: inline-block; width: 12px; height: 8px; vertical-align: middle; margin-right: 3px; border: 1px solid #d1d5db; }
        .footer { margin-top: 10px; text-align: center; font-size: 7px; color: #9ca3af; }
        .pending-marker { opacity: 0.6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $orgName }} - {{ __('app.team_calendar') }} {{ $year }}</h1>
        <p>{{ app()->getLocale() === 'de' ? 'Jahresübersicht' : 'Annual Overview' }}{{ $departmentName ? ' - ' . $departmentName : '' }}</p>
    </div>

    @foreach($monthsData as $m => $monthData)
    <div class="month-block">
        <div class="month-title">{{ $monthData['name'] }}</div>
        <table>
            <thead>
                <tr>
                    <th class="name-header">{{ __('app.employee_name') }}</th>
                    @for($d = 1; $d <= $monthData['days_in_month']; $d++)
                        @php $header = $monthData['day_headers'][$d]; @endphp
                        <th class="{{ $header['is_weekend'] ? 'weekend' : '' }}">
                            {{ $header['weekday'] }}<br>{{ $d }}
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($monthData['members'] as $memberRow)
                <tr>
                    <td class="name-cell">{{ $memberRow['member']->name }}</td>
                    @for($d = 1; $d <= $monthData['days_in_month']; $d++)
                        @php
                            $dayData = $memberRow['days'][$d];
                            $style = '';
                            $content = '';
                            $class = '';

                            if ($dayData['absence']) {
                                $absence = $dayData['absence'];
                                $color = $absence->absenceType->color ?? '#6b7280';
                                $isPending = $absence->status === 'pending';
                                $style = 'background-color: ' . $color . '; color: white; font-weight: bold;' . ($isPending ? ' opacity: 0.6;' : '');
                                $content = strtoupper(substr($absence->absenceType->name ?? 'A', 0, 1));
                                if ($isPending) {
                                    $content .= '?';
                                }
                            } elseif ($dayData['holiday']) {
                                $holiday = $dayData['holiday'];
                                if ($holiday->type === 'public_holiday') {
                                    $class = 'holiday-cell';
                                    $content = 'F';
                                } else {
                                    $class = 'school-break-cell';
                                }
                            } elseif ($dayData['is_weekend']) {
                                $class = 'weekend';
                            }
                        @endphp
                        <td class="{{ $class }}" style="{{ $style }}">{{ $content }}</td>
                    @endfor
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    {{-- Legend --}}
    <div class="legend">
        <h3>{{ __('app.legend') }}</h3>
        @foreach($absenceTypes as $type)
            <span class="legend-item">
                <span class="legend-color" style="background-color: {{ $type->color }};"></span>
                {{ $type->name }}
            </span>
        @endforeach
        <span class="legend-item">
            <span class="legend-color" style="background-color: #fee2e2; border-color: #fca5a5;"></span>
            {{ __('app.public_holidays') }}
        </span>
        <span class="legend-item">
            <span class="legend-color" style="background-color: #fef9c3; border-color: #fde68a;"></span>
            {{ __('app.school_breaks') }}
        </span>
        <span class="legend-item">
            <span class="legend-color" style="background-color: #f3f4f6;"></span>
            {{ app()->getLocale() === 'de' ? 'Wochenende' : 'Weekend' }}
        </span>
        <span class="legend-item">
            ? = {{ __('app.pending') }}
        </span>
    </div>

    <div class="footer">
        Generated by TimeCheck &middot; {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>
