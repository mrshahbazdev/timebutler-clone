@extends('layouts.app')

@section('title', __('app.calendar'))

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.calendar') }}</h1>
        <a href="{{ route('calendar.team') }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
            </svg>
            {{ app()->getLocale() === 'de' ? 'Teamkalender' : 'Team Calendar' }}
        </a>
    </div>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        {{-- Month Navigation --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50/50 to-indigo-50/50">
            <a href="{{ route('calendar', ['year' => $prevYear, 'month' => $prevMonth]) }}"
               class="inline-flex items-center gap-x-1.5 rounded-lg bg-white px-3.5 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50 hover:ring-gray-300 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                {{ __('app.previous') }}
            </a>

            <div class="text-center">
                <h2 class="text-xl font-bold text-gray-900">{{ $currentDate->translatedFormat('F Y') }}</h2>
                @if($currentDate->format('Y-m') !== now()->format('Y-m'))
                <a href="{{ route('calendar') }}" class="mt-0.5 inline-flex items-center gap-x-1 text-xs text-blue-600 hover:text-blue-500 font-semibold">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ app()->getLocale() === 'de' ? 'Heute' : 'Today' }}
                </a>
                @endif
            </div>

            <a href="{{ route('calendar', ['year' => $nextYear, 'month' => $nextMonth]) }}"
               class="inline-flex items-center gap-x-1.5 rounded-lg bg-white px-3.5 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50 hover:ring-gray-300 transition-all">
                {{ __('app.next') }}
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>

        {{-- Calendar Grid --}}
        <div class="p-4">
            {{-- Day Headers --}}
            @php
                $dayNames = app()->getLocale() === 'de'
                    ? ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag']
                    : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $dayShort = app()->getLocale() === 'de'
                    ? ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']
                    : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            <div class="grid grid-cols-7 mb-2">
                @foreach($dayNames as $i => $day)
                <div class="px-2 py-2 text-center">
                    <span class="hidden sm:inline text-xs font-bold uppercase tracking-wider {{ $i >= 5 ? 'text-gray-400' : 'text-gray-500' }}">{{ $day }}</span>
                    <span class="sm:hidden text-xs font-bold uppercase tracking-wider {{ $i >= 5 ? 'text-gray-400' : 'text-gray-500' }}">{{ $dayShort[$i] }}</span>
                </div>
                @endforeach
            </div>

            {{-- Date Cells --}}
            <div class="grid grid-cols-7 gap-1">
                @for($date = $startOfCalendar->copy(); $date <= $endOfCalendar; $date->addDay())
                    @php
                        $dateKey = $date->format('Y-m-d');
                        $isCurrentMonth = $date->month === $currentDate->month;
                        $isToday = $date->isToday();
                        $isWeekend = $date->isWeekend();
                        $dayAbsences = $absenceMap[$dateKey] ?? [];
                        $holiday = $holidays[$dateKey] ?? null;
                        $hasEvents = count($dayAbsences) > 0 || $holiday;
                    @endphp
                    <div class="relative rounded-lg min-h-[90px] p-1.5 transition-all duration-150 group
                        {{ $isToday ? 'bg-blue-50 ring-2 ring-blue-500 shadow-sm' : '' }}
                        {{ !$isCurrentMonth ? 'opacity-30' : '' }}
                        {{ $isWeekend && $isCurrentMonth && !$isToday ? 'bg-gray-50' : '' }}
                        {{ $isCurrentMonth && !$isToday && !$isWeekend ? 'hover:bg-gray-50' : '' }}
                        {{ $holiday && $isCurrentMonth ? 'bg-red-50/60' : '' }}">

                        {{-- Date Number --}}
                        <div class="flex items-center justify-between mb-1">
                            @if($isToday)
                            <span class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-blue-600 text-sm font-bold text-white shadow-sm">
                                {{ $date->day }}
                            </span>
                            @else
                            <span class="inline-flex items-center justify-center h-7 w-7 rounded-full text-sm font-medium
                                {{ $isWeekend ? 'text-gray-400' : 'text-gray-700' }}
                                {{ $isCurrentMonth ? 'group-hover:bg-gray-100' : '' }}">
                                {{ $date->day }}
                            </span>
                            @endif
                            @if(count($dayAbsences) > 2)
                            <span class="text-[9px] font-medium text-gray-400 mr-0.5">+{{ count($dayAbsences) - 2 }}</span>
                            @endif
                        </div>

                        {{-- Holiday Badge --}}
                        @if($holiday)
                        <div class="mb-0.5">
                            <span class="flex items-center gap-0.5 rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] leading-tight font-semibold text-red-700 truncate ring-1 ring-red-200/60" title="{{ $holiday->name }}">
                                <svg class="h-2.5 w-2.5 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                                </svg>
                                {{ \Illuminate\Support\Str::limit($holiday->name, 18) }}
                            </span>
                        </div>
                        @endif

                        {{-- Absence Events --}}
                        @foreach(array_slice($dayAbsences, 0, 2) as $absence)
                        <div class="mb-0.5">
                            @php
                                $isPending = $absence['status'] === 'pending';
                            @endphp
                            <span class="flex items-center rounded-md px-1.5 py-0.5 text-[10px] leading-tight font-semibold truncate transition-all
                                {{ $isPending ? 'ring-1 ring-inset' : '' }}"
                                  style="{{ $isPending
                                      ? 'background-color: ' . $absence['color'] . '15; color: ' . $absence['color'] . '; ring-color: ' . $absence['color'] . '40'
                                      : 'background-color: ' . $absence['color'] . '; color: white' }}"
                                  title="{{ $absence['type'] }} ({{ $absence['status'] === 'pending' ? (app()->getLocale() === 'de' ? 'Ausstehend' : 'Pending') : (app()->getLocale() === 'de' ? 'Genehmigt' : 'Approved') }})">
                                @if($isPending)
                                <svg class="h-2.5 w-2.5 shrink-0 mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @endif
                                {{ \Illuminate\Support\Str::limit($absence['type'], 16) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>

        {{-- Legend --}}
        <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-3.5">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mr-1">{{ app()->getLocale() === 'de' ? 'Legende' : 'Legend' }}</span>
                <div class="flex items-center gap-x-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-blue-500"></span>
                    <span class="text-xs text-gray-600">{{ __('app.vacation_leave') }}</span>
                </div>
                <div class="flex items-center gap-x-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-red-500"></span>
                    <span class="text-xs text-gray-600">{{ __('app.sick_leave') }}</span>
                </div>
                <div class="flex items-center gap-x-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-green-500"></span>
                    <span class="text-xs text-gray-600">{{ __('app.home_office') }}</span>
                </div>
                <div class="flex items-center gap-x-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-purple-500"></span>
                    <span class="text-xs text-gray-600">{{ __('app.business_trip') }}</span>
                </div>
                <div class="flex items-center gap-x-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-amber-500"></span>
                    <span class="text-xs text-gray-600">{{ app()->getLocale() === 'de' ? 'Weiterbildung' : 'Training' }}</span>
                </div>
                <div class="h-3 w-px bg-gray-300"></div>
                <div class="flex items-center gap-x-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-red-100 ring-1 ring-red-200"></span>
                    <span class="text-xs text-gray-600">{{ app()->getLocale() === 'de' ? 'Feiertag' : 'Holiday' }}</span>
                </div>
                <div class="flex items-center gap-x-1.5">
                    <span class="h-2.5 w-2.5 rounded-sm bg-blue-100 ring-1 ring-blue-300"></span>
                    <span class="text-xs text-gray-600">{{ app()->getLocale() === 'de' ? 'Ausstehend' : 'Pending' }}</span>
                </div>
                <div class="flex items-center gap-x-1.5">
                    <span class="inline-flex items-center justify-center h-4 w-4 rounded-full bg-blue-600 text-[8px] font-bold text-white">{{ now()->day }}</span>
                    <span class="text-xs text-gray-600">{{ app()->getLocale() === 'de' ? 'Heute' : 'Today' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
