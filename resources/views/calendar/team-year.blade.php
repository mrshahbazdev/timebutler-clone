@extends('layouts.app')

@section('title', __('app.team_calendar') . ' - ' . (app()->getLocale() === 'de' ? 'Jahresübersicht' : 'Annual Overview') . ' ' . $year)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.team_calendar') }} – {{ app()->getLocale() === 'de' ? 'Jahresübersicht' : 'Annual Overview' }} {{ $year }}</h1>
        <div class="flex items-center gap-x-2">
            <a href="{{ route('calendar.team', ['month' => now()->month, 'year' => $year]) }}"
               class="inline-flex items-center gap-x-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors">
                {{ app()->getLocale() === 'de' ? 'Monatsansicht' : 'Month View' }}
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

    {{-- Annual Calendar per employee --}}
    @foreach($yearData as $row)
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="border-b border-gray-200 px-4 py-3 flex items-center gap-x-3">
            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                {{ strtoupper(substr($row['member']->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">{{ $row['member']->name }}</p>
                @if($row['member']->department)
                <p class="text-xs text-gray-400">{{ $row['member']->department->name }}</p>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-[10px]">
                <thead>
                    <tr>
                        <th class="sticky left-0 z-10 bg-gray-50 px-2 py-1.5 text-left text-xs font-semibold text-gray-500 border-b border-r border-gray-200 min-w-[70px]">
                            {{ app()->getLocale() === 'de' ? 'Monat' : 'Month' }}
                        </th>
                        @for($d = 1; $d <= 31; $d++)
                        <th class="px-0 py-1 text-center font-medium text-gray-500 border-b border-gray-200 min-w-[18px]">{{ $d }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @for($m = 1; $m <= 12; $m++)
                    @php $monthName = \Carbon\Carbon::create($year, $m, 1)->translatedFormat('M'); @endphp
                    <tr class="border-b border-gray-50">
                        <td class="sticky left-0 z-10 bg-white px-2 py-1 text-xs font-medium text-gray-700 border-r border-gray-200">{{ $monthName }}</td>
                        @php $daysInMonth = \Carbon\Carbon::create($year, $m, 1)->daysInMonth; @endphp
                        @for($d = 1; $d <= 31; $d++)
                            @if($d <= $daysInMonth)
                                @php $dayData = $row['months'][$m][$d]; @endphp
                                <td class="px-0 py-0.5 text-center">
                                    @if($dayData['absence'])
                                        <div class="h-4 w-full rounded-sm" style="background-color: {{ $dayData['absence']->absenceType->color ?? '#6b7280' }}; opacity: {{ $dayData['absence']->status === 'pending' ? '0.5' : '1' }}"
                                             title="{{ $dayData['absence']->absenceType->name ?? '' }}"></div>
                                    @elseif($dayData['holiday'])
                                        <div class="h-4 w-full rounded-sm bg-red-100" title="{{ $dayData['holiday']->name_de ?? $dayData['holiday']->name }}"></div>
                                    @elseif($dayData['is_weekend'])
                                        <div class="h-4 w-full bg-gray-100 rounded-sm"></div>
                                    @else
                                        <div class="h-4 w-full"></div>
                                    @endif
                                </td>
                            @else
                                <td class="px-0 py-0.5 bg-gray-50"></td>
                            @endif
                        @endfor
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

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
