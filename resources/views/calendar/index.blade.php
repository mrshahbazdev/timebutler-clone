@extends('layouts.app')

@section('title', __('app.calendar'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.calendar') }}</h1>
        <a href="{{ route('calendar.team') }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
            </svg>
            {{ app()->getLocale() === 'de' ? 'Teamkalender' : 'Team Calendar' }}
        </a>
    </div>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        {{-- Month Navigation --}}
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('calendar', ['year' => $prevYear, 'month' => $prevMonth]) }}"
               class="inline-flex items-center gap-x-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                {{ __('app.previous') }}
            </a>

            <div class="text-center">
                <h2 class="text-lg font-bold text-gray-900">{{ $currentDate->translatedFormat('F Y') }}</h2>
                @if($currentDate->format('Y-m') !== now()->format('Y-m'))
                <a href="{{ route('calendar') }}" class="text-xs text-blue-600 hover:text-blue-500 font-medium">
                    {{ app()->getLocale() === 'de' ? 'Heute' : 'Today' }}
                </a>
                @endif
            </div>

            <a href="{{ route('calendar', ['year' => $nextYear, 'month' => $nextMonth]) }}"
               class="inline-flex items-center gap-x-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                {{ __('app.next') }}
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>

        {{-- Calendar Grid --}}
        <div class="grid grid-cols-7 gap-px bg-gray-200 rounded-lg overflow-hidden">
            @php
                $dayNames = app()->getLocale() === 'de'
                    ? ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']
                    : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach($dayNames as $day)
            <div class="bg-gray-50 px-3 py-2 text-center text-xs font-semibold text-gray-500">{{ $day }}</div>
            @endforeach

            @for($date = $startOfCalendar->copy(); $date <= $endOfCalendar; $date->addDay())
                @php
                    $dateKey = $date->format('Y-m-d');
                    $isCurrentMonth = $date->month === $currentDate->month;
                    $isToday = $date->isToday();
                    $isWeekend = $date->isWeekend();
                    $dayAbsences = $absenceMap[$dateKey] ?? [];
                    $holiday = $holidays[$dateKey] ?? null;
                @endphp
                <div class="bg-white px-2 py-1.5 min-h-[85px] {{ !$isCurrentMonth ? 'opacity-40' : '' }} {{ $isToday ? 'ring-2 ring-inset ring-blue-500' : '' }} {{ $isWeekend && $isCurrentMonth ? 'bg-gray-50/50' : '' }}">
                    <span class="text-sm {{ $isToday ? 'font-bold text-white bg-blue-600 rounded-full h-6 w-6 inline-flex items-center justify-center' : ($isWeekend ? 'text-gray-400' : 'text-gray-700') }}">{{ $date->day }}</span>

                    @if($holiday)
                    <div class="mt-1">
                        <span class="block text-[10px] leading-tight font-medium text-red-600 truncate" title="{{ $holiday->name }}">
                            {{ $holiday->name }}
                        </span>
                    </div>
                    @endif

                    @foreach($dayAbsences as $absence)
                    <div class="mt-0.5">
                        <span class="block rounded px-1 py-0.5 text-[10px] leading-tight font-medium text-white truncate"
                              style="background-color: {{ $absence['color'] }}{{ $absence['status'] === 'pending' ? '99' : '' }}"
                              title="{{ $absence['type'] }} ({{ $absence['status'] }})">
                            {{ $absence['type'] }}
                            @if($absence['status'] === 'pending')
                                <span class="opacity-75">?</span>
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            @endfor
        </div>

        {{-- Legend --}}
        <div class="mt-4 flex flex-wrap gap-4">
            <div class="flex items-center gap-x-2">
                <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                <span class="text-xs text-gray-600">{{ __('app.vacation_leave') }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-3 w-3 rounded-full bg-red-500"></div>
                <span class="text-xs text-gray-600">{{ __('app.sick_leave') }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-3 w-3 rounded-full bg-green-500"></div>
                <span class="text-xs text-gray-600">{{ __('app.home_office') }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-3 w-3 rounded-full bg-purple-500"></div>
                <span class="text-xs text-gray-600">{{ __('app.business_trip') }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-3 w-3 rounded-full bg-amber-500"></div>
                <span class="text-xs text-gray-600">{{ app()->getLocale() === 'de' ? 'Weiterbildung' : 'Training' }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-3 w-3 rounded bg-red-600"></div>
                <span class="text-xs text-gray-600">{{ app()->getLocale() === 'de' ? 'Feiertag' : 'Holiday' }}</span>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="h-3 w-3 rounded-full bg-gray-400 opacity-60"></div>
                <span class="text-xs text-gray-600">{{ app()->getLocale() === 'de' ? 'Ausstehend' : 'Pending' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
