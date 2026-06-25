@extends('layouts.app')

@section('title', __('app.team_calendar'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.team_calendar') }}</h1>
        <div class="flex items-center gap-x-2">
            <a href="{{ route('calendar.team.year', ['year' => $year, 'department_id' => $departmentId]) }}"
               class="inline-flex items-center gap-x-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                {{ app()->getLocale() === 'de' ? 'Jahresübersicht' : 'Annual Overview' }}
            </a>
            <a href="{{ route('calendar.team.pdf', ['month' => $month, 'year' => $year, 'department_id' => $departmentId]) }}"
               class="inline-flex items-center gap-x-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.75 3.75h-1.5" />
                </svg>
                {{ __('app.print') }} PDF
            </a>
        </div>
    </div>

    {{-- Month Navigation --}}
    <div class="flex items-center justify-between rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-4">
        <a href="{{ route('calendar.team', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year, 'department_id' => $departmentId]) }}"
           class="rounded-lg p-2 hover:bg-gray-100 transition-colors">
            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">{{ $startOfMonth->translatedFormat('F Y') }}</h2>
        <a href="{{ route('calendar.team', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year, 'department_id' => $departmentId]) }}"
           class="rounded-lg p-2 hover:bg-gray-100 transition-colors">
            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </a>
    </div>

    {{-- Department Filter --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('calendar.team', ['month' => $month, 'year' => $year]) }}"
           class="rounded-lg px-3 py-1.5 text-sm font-medium {{ !$departmentId ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
            {{ __('app.all') }}
        </a>
        @foreach($departments as $dept)
        <a href="{{ route('calendar.team', ['month' => $month, 'year' => $year, 'department_id' => $dept->id]) }}"
           class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $departmentId == $dept->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
            <span class="inline-block h-2 w-2 rounded-full mr-1" style="background-color: {{ $dept->color }}"></span>
            {{ $dept->name }}
        </a>
        @endforeach
    </div>

    {{-- Calendar Grid --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr>
                    <th class="sticky left-0 z-10 bg-gray-50 px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase border-b border-r border-gray-200 min-w-[150px]">
                        {{ __('app.employee_name') }}
                    </th>
                    @for($d = 1; $d <= $daysInMonth; $d++)
                    @php $date = \Carbon\Carbon::create($year, $month, $d); @endphp
                    <th class="px-0.5 py-2 text-center text-xs border-b border-gray-200 min-w-[28px] {{ $date->isWeekend() ? 'bg-gray-100' : 'bg-gray-50' }}">
                        <div class="font-medium text-gray-500">{{ $date->format('D')[0] }}</div>
                        <div class="font-semibold {{ $date->isToday() ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center mx-auto' : 'text-gray-700' }}">
                            {{ $d }}
                        </div>
                    </th>
                    @endfor
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($calendarData as $row)
                <tr class="hover:bg-gray-50/50">
                    <td class="sticky left-0 z-10 bg-white px-4 py-2 border-r border-gray-200">
                        <div class="flex items-center gap-x-2">
                            <div class="h-7 w-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ strtoupper(substr($row['member']->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $row['member']->name }}</p>
                                @if($row['member']->department)
                                <p class="text-xs text-gray-400 truncate">{{ $row['member']->department->name }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    @foreach($row['days'] as $d => $dayData)
                    <td class="px-0.5 py-1 text-center {{ $dayData['is_weekend'] ? 'bg-gray-50' : '' }}">
                        @if($dayData['absence'])
                            @php $absence = $dayData['absence']; @endphp
                            <div class="h-6 w-full rounded-sm flex items-center justify-center text-white text-[10px] font-bold cursor-default"
                                 style="background-color: {{ $absence->absenceType->color ?? '#6b7280' }}; opacity: {{ $absence->status === 'pending' ? '0.6' : '1' }}"
                                 title="{{ $absence->absenceType->name ?? '' }} ({{ __('app.' . $absence->status) }})">
                                {{ strtoupper(substr($absence->absenceType->icon ?? $absence->absenceType->name ?? 'A', 0, 1)) }}
                            </div>
                        @elseif($dayData['holidays'] && $dayData['holidays']->isNotEmpty())
                            @php
                                $isPublicHoliday = $dayData['holidays']->contains('type', 'public_holiday');
                                $isSchoolBreak = $dayData['holidays']->contains('type', 'school_break');
                                $titles = $dayData['holidays']->map(fn($h) => $h->name_de ?? $h->name)->join(', ');
                            @endphp
                            <div class="h-6 w-full rounded-sm {{ $isPublicHoliday && $isSchoolBreak ? 'bg-red-100 border-b-2 border-dashed border-amber-400' : ($isPublicHoliday ? 'bg-red-100' : 'bg-amber-50 border-b border-dashed border-amber-300') }} flex items-center justify-center" title="{{ $titles }}">
                                @if($isPublicHoliday)
                                    <span class="text-red-500 text-[10px] font-bold">F</span>
                                @endif
                            </div>
                        @elseif($dayData['is_weekend'])
                            <div class="h-6 w-full bg-gray-100 rounded-sm"></div>
                        @else
                            <div class="h-6 w-full"></div>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($teamMembers->hasPages())
        <div class="border-t border-gray-200 px-6 py-3 bg-white">
            {{ $teamMembers->links() }}
        </div>
        @endif
    </div>

    {{-- Legend --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('app.legend') }}</h3>
        <div class="flex flex-wrap gap-4">
            @php
                $absenceTypes = \App\Models\AbsenceType::where('organization_id', auth()->user()->organization_id)->where('is_active', true)->orderBy('sort_order')->get();
            @endphp
            @foreach($absenceTypes as $type)
            <div class="flex items-center gap-x-2">
                <div class="h-4 w-6 rounded-sm" style="background-color: {{ $type->color }}"></div>
                <span class="text-xs text-gray-600">{{ $type->name }}</span>
            </div>
            @endforeach
            <div class="flex items-center gap-x-2">
                <div class="h-4 w-6 rounded-sm bg-red-100 border border-red-200"></div>
                <span class="text-xs text-gray-600">{{ __('app.public_holidays') }} / {{ __('app.school_breaks') }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-4 w-6 rounded-sm bg-gray-100 border border-gray-200"></div>
                <span class="text-xs text-gray-600">{{ __('app.weekends') }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-4 w-6 rounded-sm bg-blue-400 opacity-60 border border-blue-300"></div>
                <span class="text-xs text-gray-600">{{ __('app.pending') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
