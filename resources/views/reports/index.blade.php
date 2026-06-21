@extends('layouts.app')

@section('title', __('app.reports'))

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('app.reports') }}</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        {{-- Absence Report --}}
        <a href="{{ route('reports.absences') }}" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:ring-blue-200 hover:shadow-md transition-all">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 mb-4">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900">{{ __('app.absences') }}</h3>
            <p class="mt-1 text-xs text-gray-500">PDF & Excel export</p>
        </a>

        {{-- Time Report --}}
        <a href="{{ route('reports.time-tracking') }}" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:ring-green-200 hover:shadow-md transition-all">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-50 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900">{{ __('app.time_tracking') }}</h3>
            <p class="mt-1 text-xs text-gray-500">PDF & Excel export</p>
        </a>

        {{-- Overtime Report --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 opacity-60">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-50 mb-4">
                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900">{{ __('app.overtime') }}</h3>
            <p class="mt-1 text-xs text-gray-500">Coming soon</p>
        </div>
    </div>
</div>
@endsection
