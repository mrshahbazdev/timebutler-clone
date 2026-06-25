@extends('layouts.app')

@section('title', __('app.overtime'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.overtime') }}</h1>
        @can('manage_overtime')
        <a href="{{ route('overtime.admin') }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
            </svg>
            {{ app()->getLocale() === 'de' ? 'Überstunden verwalten' : 'Manage Overtime' }}
        </a>
        @endcan
    </div>

    @php
        $totalAbs = abs($totalMinutes);
        $totalSign = $totalMinutes >= 0 ? '+' : '-';
        $totalFormatted = sprintf('%s%d:%02d', $totalSign, intdiv($totalAbs, 60), $totalAbs % 60);

        $thisMonthMin = $thisMonthBalance ? $thisMonthBalance->balance_minutes : 0;
        $thisMonthAbs = abs($thisMonthMin);
        $thisMonthSign = $thisMonthMin >= 0 ? '+' : '-';
        $thisMonthFormatted = sprintf('%s%d:%02d', $thisMonthSign, intdiv($thisMonthAbs, 60), $thisMonthAbs % 60);
    @endphp

    {{-- Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('app.overtime_balance') }}</p>
            <p class="mt-2 text-3xl font-bold {{ $totalMinutes >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $totalFormatted }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('app.hours') }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ app()->getLocale() === 'de' ? 'Dieser Monat' : 'This Month' }}</p>
            <p class="mt-2 text-3xl font-bold {{ $thisMonthMin >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $thisMonthFormatted }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('app.hours') }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ app()->getLocale() === 'de' ? 'Ziel / Woche' : 'Target / Week' }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ auth()->user()->weekly_hours ?? 40 }}h</p>
            <p class="mt-1 text-xs text-gray-500">{{ (auth()->user()->weekly_hours ?? 40) / 5 }}h / {{ app()->getLocale() === 'de' ? 'Tag' : 'day' }}</p>
        </div>
    </div>

    {{-- Monthly Overview --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">{{ app()->getLocale() === 'de' ? 'Monatsübersicht' : 'Monthly Overview' }}</h2>
        <div class="space-y-3">
            @if($balances->isEmpty())
            <p class="text-sm text-gray-500 text-center py-8">{{ __('app.no_data') }}</p>
            @else
            @php $maxAbs = max($balances->max(fn($b) => abs($b->balance_minutes)), 1); @endphp
            @foreach($balances as $balance)
            @php
                $monthLabel = \Carbon\Carbon::create($balance->year, $balance->month, 1)->translatedFormat('F Y');
            @endphp
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <span class="text-sm text-gray-700">{{ $monthLabel }}</span>
                <div class="flex items-center gap-x-4">
                    <span class="text-sm font-medium {{ $balance->balance_minutes >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $balance->formatted_balance }}
                    </span>
                    <div class="h-2 w-24 rounded-full bg-gray-100">
                        <div class="h-2 rounded-full {{ $balance->balance_minutes >= 0 ? 'bg-green-500' : 'bg-red-500' }}"
                             style="width: {{ min(100, (abs($balance->balance_minutes) / $maxAbs) * 100) }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
