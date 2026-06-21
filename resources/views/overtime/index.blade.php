@extends('layouts.app')

@section('title', __('app.overtime'))

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('app.overtime') }}</h1>

    {{-- Overview Card --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">{{ __('app.overtime_balance') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">+12:30</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('app.hours') }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">This Month</p>
            <p class="mt-2 text-3xl font-bold text-green-600">+3:45</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('app.hours') }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-sm font-medium text-gray-500">Target / Week</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ auth()->user()->weekly_hours ?? 40 }}h</p>
            <p class="mt-1 text-xs text-gray-500">{{ (auth()->user()->weekly_hours ?? 40) / 5 }}h / day</p>
        </div>
    </div>

    {{-- Monthly Overview --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Monthly Overview</h2>
        <div class="space-y-3">
            @for($i = 0; $i < 6; $i++)
            @php $month = now()->subMonths($i); @endphp
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <span class="text-sm text-gray-700">{{ $month->translatedFormat('F Y') }}</span>
                <div class="flex items-center gap-x-4">
                    <span class="text-sm font-medium {{ $i % 2 === 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $i % 2 === 0 ? '+' : '-' }}{{ rand(1,15) }}:{{ str_pad(rand(0,59), 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <div class="h-2 w-24 rounded-full bg-gray-100">
                        <div class="h-2 rounded-full {{ $i % 2 === 0 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ rand(20, 80) }}%"></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
@endsection
