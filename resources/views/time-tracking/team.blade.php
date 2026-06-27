@extends('layouts.app')

@section('title', app()->getLocale() === 'de' ? 'Team-Zeiterfassung' : 'Team Time Tracking')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Team-Zeiterfassung' : 'Team Time Tracking' }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ app()->getLocale() === 'de' ? 'Zeiterfassung aller Mitarbeiter anzeigen' : 'View time tracking entries for all employees' }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-4">
        <form action="{{ route('time-tracking.team') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.start_date') }}</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="block rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.end_date') }}</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="block rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.employee_name') }}</label>
                <select name="employee_id" class="block rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">{{ app()->getLocale() === 'de' ? 'Alle Mitarbeiter' : 'All Employees' }}</option>
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

    {{-- Table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.employee_name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.start_date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.start_time') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.end_time') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.break') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.total_hours') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.project') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($entries as $entry)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <a href="{{ route('employees.show', $entry->user) }}" class="hover:text-blue-600 transition-colors">
                                {{ $entry->user->name ?? '-' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $entry->date->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $entry->start_time ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $entry->end_time ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600">{{ $entry->break_minutes }}m</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold text-gray-900">{{ $entry->formatted_hours }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $entry->project ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                {{ $entry->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $entry->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $entry->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}">
                                {{ __('app.' . $entry->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-500 font-medium">{{ __('app.no_data') }}</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($entries->hasPages())
        <div class="border-t border-gray-100 px-6 py-4">
            {{ $entries->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
