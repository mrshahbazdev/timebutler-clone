@extends('layouts.app')

@section('title', __('app.time_tracking') . ' - ' . __('app.reports'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-x-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                {{ __('app.reports') }}
            </a>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ __('app.time_tracking') }} Report</h1>
        </div>
        <div class="flex items-center gap-x-2">
            <a href="{{ route('reports.time-tracking.pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'employee_id' => $employeeId]) }}"
               class="inline-flex items-center gap-x-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                PDF
            </a>
            <a href="{{ route('reports.time-tracking.excel', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'employee_id' => $employeeId]) }}"
               class="inline-flex items-center gap-x-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375" />
                </svg>
                Excel
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-4">
        <form action="{{ route('reports.time-tracking') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.start_date') }}</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="block rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.end_date') }}</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="block rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.employee_name') }}</label>
                <select name="employee_id" class="block rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors">
                {{ __('app.filter') }}
            </button>
        </form>
    </div>

    {{-- Summary --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5">
        <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-gray-500">{{ __('app.total_hours') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalHours }}h {{ $remainingMinutes }}m</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.employee_name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.start_date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.start_time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.end_time') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.break') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.total_hours') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.project') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($entries as $entry)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-sm text-gray-900">{{ $entry->user->name ?? '-' }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $entry->date->format('d.m.Y') }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $entry->start_time ?? '-' }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $entry->end_time ?? '-' }}</td>
                    <td class="px-6 py-3 text-sm text-center text-gray-600">{{ $entry->break_minutes }}m</td>
                    <td class="px-6 py-3 text-sm text-center font-semibold">{{ $entry->formatted_hours }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $entry->project ?? '-' }}</td>
                    <td class="px-6 py-3 text-center">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $entry->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $entry->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $entry->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ __('app.' . $entry->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('app.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
