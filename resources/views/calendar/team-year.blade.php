@extends('layouts.app')

@section('title', __('app.team_calendar') . ' - ' . (app()->getLocale() === 'de' ? 'Jahresübersicht' : 'Annual Overview') . ' ' . $year)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.team_calendar') }} – {{ app()->getLocale() === 'de' ? 'Jahresübersicht' : 'Annual Overview' }} {{ $year }}</h1>
        <div class="flex items-center gap-x-2">
            <a href="{{ route('calendar.team', ['month' => now()->month, 'year' => $year]) }}"
               class="inline-flex items-center gap-x-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                {{ app()->getLocale() === 'de' ? 'Monatsansicht' : 'Month View' }}
            </a>
            <a href="{{ route('calendar.team.year.pdf', ['year' => $year, 'department_id' => $departmentId]) }}"
               class="inline-flex items-center gap-x-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.75 3.75h-1.5" />
                </svg>
                {{ __('app.print') }} PDF
            </a>
        </div>
    </div>

    {{-- Year Navigation --}}
    <div class="flex items-center justify-between rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-4">
        <a href="{{ route('calendar.team.year', ['year' => $year - 1, 'department_id' => $departmentId]) }}"
           class="rounded-lg p-2 hover:bg-gray-100 transition-colors">
            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">{{ $year }}</h2>
        <a href="{{ route('calendar.team.year', ['year' => $year + 1, 'department_id' => $departmentId]) }}"
           class="rounded-lg p-2 hover:bg-gray-100 transition-colors">
            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </a>
    </div>

    {{-- Department Filter --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('calendar.team.year', ['year' => $year]) }}"
           class="rounded-lg px-3 py-1.5 text-sm font-medium {{ !$departmentId ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
            {{ __('app.all') }}
        </a>
        @foreach($departments as $dept)
        <a href="{{ route('calendar.team.year', ['year' => $year, 'department_id' => $dept->id]) }}"
           class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $departmentId == $dept->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
            <span class="inline-block h-2 w-2 rounded-full mr-1" style="background-color: {{ $dept->color }}"></span>
            {{ $dept->name }}
        </a>
        @endforeach
    </div>

    {{-- Month Tables --}}
    @foreach($monthsData as $m => $monthData)
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
            <h3 class="text-base font-bold text-gray-900">{{ $monthData['name'] }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    {{-- Row 1: School breaks label + weekday names --}}
                    <tr class="bg-gray-50">
                        <th class="sticky left-0 z-10 bg-gray-50 px-3 py-1 text-left text-[10px] font-medium text-gray-400 border-b border-r border-gray-200 min-w-[150px]"></th>
                        @php $prevWeek = null; @endphp
                        @for($d = 1; $d <= $monthData['days_in_month']; $d++)
                            @php
                                $header = $monthData['day_headers'][$d];
                                $currentWeek = $header['week_number'];
                                $showWeekSep = $prevWeek !== null && $currentWeek !== $prevWeek;
                                $prevWeek = $currentWeek;
                            @endphp
                            <th class="px-0 py-1 text-center text-[10px] font-medium border-b border-gray-200 min-w-[26px] {{ $header['is_weekend'] ? 'bg-gray-200 text-gray-500' : 'text-gray-500' }} {{ $showWeekSep ? 'border-l-2 border-l-gray-300' : '' }}">
                                {{ $header['weekday'] }}
                            </th>
                        @endfor
                    </tr>
                    {{-- Row 2: Week numbers + day numbers --}}
                    <tr class="bg-gray-50">
                        <th class="sticky left-0 z-10 bg-gray-50 px-3 py-1 text-left text-[10px] font-medium text-gray-400 border-b border-r border-gray-200 min-w-[150px]"></th>
                        @php $prevWeek = null; @endphp
                        @for($d = 1; $d <= $monthData['days_in_month']; $d++)
                            @php
                                $header = $monthData['day_headers'][$d];
                                $currentWeek = $header['week_number'];
                                $showWeekSep = $prevWeek !== null && $currentWeek !== $prevWeek;
                                $prevWeek = $currentWeek;
                            @endphp
                            <th class="px-0 py-1 text-center text-[11px] font-semibold border-b border-gray-200 min-w-[26px] {{ $header['is_weekend'] ? 'bg-gray-200 text-gray-500' : 'text-gray-700' }} {{ $header['is_today'] ? 'bg-blue-600 text-white' : '' }} {{ $showWeekSep ? 'border-l-2 border-l-gray-300' : '' }}">
                                {{ $d }}
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($monthData['members'] as $memberRow)
                    <tr class="hover:bg-gray-50/50">
                        <td class="sticky left-0 z-10 bg-white px-3 py-1.5 border-r border-gray-200">
                            <div class="flex items-center gap-x-2">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-white text-[9px] font-bold shrink-0" style="background-color: {{ $memberRow['member']->department?->color ?? '#6366f1' }}">
                                    {{ strtoupper(substr($memberRow['member']->name, 0, 1)) }}
                                </div>
                                <span class="text-xs font-medium text-gray-900 truncate max-w-[120px]">{{ $memberRow['member']->name }}</span>
                            </div>
                        </td>
                        @php $prevWeek = null; @endphp
                        @for($d = 1; $d <= $monthData['days_in_month']; $d++)
                            @php
                                $dayData = $memberRow['days'][$d];
                                $header = $monthData['day_headers'][$d];
                                $currentWeek = $header['week_number'];
                                $showWeekSep = $prevWeek !== null && $currentWeek !== $prevWeek;
                                $prevWeek = $currentWeek;
                            @endphp
                            <td class="px-0 py-0.5 text-center {{ $showWeekSep ? 'border-l-2 border-l-gray-300' : '' }}">
                                @if($dayData['absence'])
                                    @php $absence = $dayData['absence']; @endphp
                                    <div class="h-5 w-full flex items-center justify-center text-white text-[9px] font-bold cursor-default"
                                         style="background-color: {{ $absence->absenceType->color ?? '#6b7280' }}; opacity: {{ $absence->status === 'pending' ? '0.6' : '1' }}"
                                         title="{{ $absence->absenceType->name ?? '' }} ({{ $absence->status }})">
                                        @if($absence->status === 'pending')
                                            X
                                        @endif
                                    </div>
                                @elseif($dayData['holiday'])
                                    @php $holiday = $dayData['holiday']; @endphp
                                    @if($holiday->type === 'public_holiday')
                                        <div class="h-5 w-full bg-amber-100" title="{{ $holiday->name_de ?? $holiday->name }}"></div>
                                    @else
                                        <div class="h-5 w-full bg-amber-50 border-b border-dashed border-amber-300" title="{{ $holiday->name_de ?? $holiday->name }}"></div>
                                    @endif
                                @elseif($dayData['is_weekend'])
                                    <div class="h-5 w-full bg-gray-200"></div>
                                @else
                                    <div class="h-5 w-full"></div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    {{-- Legend --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('app.legend') }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <div class="flex items-center gap-x-2">
                <div class="h-4 w-8 bg-gray-200 border border-gray-300"></div>
                <span class="text-xs text-gray-600">{{ app()->getLocale() === 'de' ? 'Wochenende (Sa/So)' : 'Weekend (Sat/Sun)' }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-4 w-8 bg-amber-100 border border-amber-200"></div>
                <span class="text-xs text-gray-600">{{ __('app.public_holidays') }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-4 w-8 bg-amber-50 border-b border-dashed border-amber-300"></div>
                <span class="text-xs text-gray-600">{{ __('app.school_breaks') }}</span>
            </div>
            @foreach($absenceTypes as $type)
            <div class="flex items-center gap-x-2">
                <div class="h-4 w-8 border" style="background-color: {{ $type->color }}"></div>
                <span class="text-xs text-gray-600">{{ $type->name }}</span>
            </div>
            @endforeach
            <div class="flex items-center gap-x-2">
                <div class="h-4 w-8 bg-blue-400 opacity-60 border border-blue-300 flex items-center justify-center text-white text-[9px] font-bold">X</div>
                <span class="text-xs text-gray-600">{{ __('app.pending') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
