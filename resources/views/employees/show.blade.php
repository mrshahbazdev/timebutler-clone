@extends('layouts.app')

@section('title', $employee->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-x-4">
            <a href="{{ route('employees.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div class="h-14 w-14 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold">
                {{ strtoupper(substr($employee->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $employee->name }}</h1>
                <p class="text-sm text-gray-500">{{ $employee->position ?? '' }} {{ $employee->department ? '· ' . $employee->department->name : '' }}</p>
            </div>
        </div>
        <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
            </svg>
            {{ __('app.edit') }}
        </a>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-xs font-medium text-gray-500">{{ __('app.employee_email') }}</p>
            <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->email }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-xs font-medium text-gray-500">{{ __('app.employee_manager') }}</p>
            <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->manager->name ?? '-' }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-xs font-medium text-gray-500">Weekly Hours</p>
            <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->weekly_hours }}h</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5">
            <p class="text-xs font-medium text-gray-500">{{ __('app.my_vacation_balance') }}</p>
            <p class="mt-1 text-sm font-medium text-gray-900">
                {{ $vacationBalance ? $vacationBalance->remaining_days : $employee->vacation_days_per_year }}
                <span class="text-gray-500 font-normal">/ {{ $employee->vacation_days_per_year }} {{ __('app.days') }}</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Absences --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900">{{ __('app.absences') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentAbsences as $absence)
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-x-3">
                        <div class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $absence->absenceType->color ?? '#6b7280' }}"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $absence->absenceType->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $absence->start_date->format('d.m.Y') }} - {{ $absence->end_date->format('d.m.Y') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                        {{ $absence->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $absence->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $absence->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ __('app.' . $absence->status) }}
                    </span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-500">{{ __('app.no_data') }}</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Time Entries --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900">{{ __('app.time_tracking') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentTimeEntries as $entry)
                <div class="flex items-center justify-between px-6 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $entry->date->format('d.m.Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $entry->start_time ?? '-' }} - {{ $entry->end_time ?? '-' }}</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ $entry->formatted_hours }}</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-500">{{ __('app.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Details</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <dt class="text-xs font-medium text-gray-500">Employee #</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $employee->employee_number ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Phone</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $employee->phone ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Role</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($employee->roles->first()?->name ?? '-') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">{{ __('app.language') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $employee->locale === 'de' ? 'Deutsch' : 'English' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Employment Start</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $employee->employment_start?->format('d.m.Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">{{ __('app.status') }}</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $employee->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $employee->is_active ? __('app.active') : __('app.inactive') }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>
</div>
@endsection
