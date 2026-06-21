@extends('layouts.app')

@section('title', __('app.calendar'))

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('app.calendar') }}</h1>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">{{ now()->translatedFormat('F Y') }}</h2>
            <div class="flex gap-2">
                <button class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('app.previous') }}</button>
                <button class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('app.next') }}</button>
            </div>
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

            @php
                $startOfMonth = now()->startOfMonth();
                $startOfCalendar = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                $endOfMonth = now()->endOfMonth();
                $endOfCalendar = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
            @endphp

            @for($date = $startOfCalendar->copy(); $date <= $endOfCalendar; $date->addDay())
            <div class="bg-white px-3 py-2 min-h-[80px] {{ $date->month !== now()->month ? 'opacity-50' : '' }} {{ $date->isToday() ? 'ring-2 ring-inset ring-blue-500' : '' }}">
                <span class="text-sm {{ $date->isToday() ? 'font-bold text-blue-600' : 'text-gray-700' }}">{{ $date->day }}</span>
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
        </div>
    </div>
</div>
@endsection
