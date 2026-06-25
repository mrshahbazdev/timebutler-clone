@extends('layouts.app')

@section('title', __('app.holidays'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.holidays') }} & {{ __('app.school_breaks') }}</h1>
    </div>

    {{-- Import Form (Admin only) --}}
    @can('manage_holidays')
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('app.import_calendar') }}</h2>
        <form action="{{ route('holidays.import') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.federal_state') }}</label>
                    <select name="state" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @foreach($states as $code => $name)
                        <option value="{{ $code }}" {{ $state === $code ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.year') }}</label>
                    <select name="year" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @for($y = now()->year; $y <= now()->year + 3; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-x-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        {{ __('app.import') }}
                    </button>
                </div>
            </div>

            <div class="flex flex-wrap gap-x-6 gap-y-2">
                <label class="inline-flex items-center gap-x-2 text-sm text-gray-700">
                    <input type="checkbox" name="import_public_holidays" value="1" checked
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    {{ __('app.public_holidays') }}
                </label>
                <label class="inline-flex items-center gap-x-2 text-sm text-gray-700">
                    <input type="checkbox" name="import_school_breaks" value="1" checked
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    {{ __('app.school_breaks') }}
                </label>
                <label class="inline-flex items-center gap-x-2 text-sm text-gray-700">
                    <input type="checkbox" name="import_weekends" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    {{ __('app.weekends') }}
                </label>
            </div>

            <p class="text-xs text-gray-500">
                {{ __('app.school_breaks_source') }}:
                <a href="https://ferien-api.de" target="_blank" class="text-blue-600 hover:underline">ferien-api.de</a>
            </p>
        </form>
    </div>
    @endcan

    {{-- Year Filter --}}
    <div class="flex items-center gap-x-2">
        @for($y = now()->year; $y <= now()->year + 3; $y++)
        <a href="{{ route('holidays.index', ['year' => $y, 'state' => $state]) }}"
           class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $year == $y ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
            {{ $y }}
        </a>
        @endfor
    </div>

    @php $allHolidays = collect($holidays)->flatten(1); @endphp

    @if($allHolidays->isEmpty())
    <div class="rounded-xl bg-white p-12 shadow-sm ring-1 ring-gray-900/5 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
        </svg>
        <p class="mt-2 text-sm text-gray-500">{{ __('app.no_holidays_imported') }}</p>
        <p class="text-xs text-gray-400">{{ __('app.use_import_form') }}</p>
    </div>
    @else

    {{-- Public Holidays --}}
    @if(isset($holidays['public_holiday']) && $holidays['public_holiday']->count())
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-x-2">
                <span class="h-3 w-3 rounded-full bg-red-500"></span>
                <h2 class="text-sm font-semibold text-gray-900">{{ __('app.public_holidays') }}</h2>
            </div>
            <span class="text-xs text-gray-500">{{ $holidays['public_holiday']->count() }} {{ __('app.days') }}</span>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($holidays['public_holiday']->sortBy('date') as $h)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ app()->getLocale() === 'de' ? ($h->name_de ?: $h->name) : $h->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">{{ $h->date->format('d.m.Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $h->date->translatedFormat('l') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- School Breaks --}}
    @if(isset($holidays['school_break']) && $holidays['school_break']->count())
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-x-2">
                <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                <h2 class="text-sm font-semibold text-gray-900">{{ __('app.school_breaks') }}</h2>
            </div>
            <span class="text-xs text-gray-500">{{ $holidays['school_break']->count() }} {{ __('app.days') }}</span>
        </div>
        <div class="divide-y divide-gray-100">
            @php
                $grouped = $holidays['school_break']->groupBy(function($h) { return $h->name_de ?: $h->name; });
            @endphp
            @foreach($grouped as $breakName => $days)
            <div class="flex items-center justify-between px-6 py-3">
                <p class="text-sm font-medium text-gray-900">{{ $breakName }}</p>
                <div class="text-right">
                    <p class="text-sm text-gray-600">{{ $days->min('date')->format('d.m.Y') }} - {{ $days->max('date')->format('d.m.Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $days->count() }} {{ __('app.days') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Delete All (Admin only) --}}
    @can('manage_holidays')
    <div class="flex justify-end">
        <form action="{{ route('holidays.destroy') }}" method="POST" onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
            @csrf
            @method('DELETE')
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="inline-flex items-center gap-x-2 rounded-lg bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                {{ __('app.delete_all_holidays') }} {{ $year }}
            </button>
        </form>
    </div>
    @endcan
    @endif
</div>
@endsection
