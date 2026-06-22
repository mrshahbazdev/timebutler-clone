@extends('layouts.app')

@section('title', __('app.dashboard'))

@section('content')
<div class="space-y-6">
    {{-- Welcome Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('app.welcome_back') }}, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('app.todays_overview') }} &mdash; {{ now()->translatedFormat('l, d. F Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('absences.create') }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('app.new_absence_request') }}
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Vacation Balance --}}
        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center gap-x-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('app.my_vacation_balance') }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $vacationBalance ? $vacationBalance->remaining_days : 30 }}
                        <span class="text-sm font-normal text-gray-500">{{ __('app.days') }}</span>
                    </p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>{{ $vacationBalance ? $vacationBalance->used_days : 0 }} {{ __('app.days') }} used</span>
                    <span>{{ $vacationBalance ? $vacationBalance->total_days : 30 }} total</span>
                </div>
                <div class="h-2 w-full rounded-full bg-gray-100">
                    @php $usedPercent = $vacationBalance ? ($vacationBalance->used_days / max($vacationBalance->total_days, 1)) * 100 : 0; @endphp
                    <div class="h-2 rounded-full bg-blue-600 transition-all" style="width: {{ $usedPercent }}%"></div>
                </div>
            </div>
        </div>

        {{-- Hours Today --}}
        @php
            $dashRunning = $todayEntry && $todayEntry->start_time && !$todayEntry->end_time;
        @endphp
        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5"
             @if($dashRunning)
             x-data="{
                startTime: new Date('{{ now()->format('Y-m-d') }}T{{ $todayEntry->start_time }}'),
                display: '0:00',
                init() { this.tick(); setInterval(() => this.tick(), 1000); },
                tick() {
                    let s = Math.floor((Date.now() - this.startTime.getTime()) / 1000);
                    let m = Math.floor(s / 60);
                    this.display = Math.floor(m / 60) + ':' + String(m % 60).padStart(2, '0');
                }
             }"
             @endif
        >
            <div class="flex items-center gap-x-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-50">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('app.hours_today') }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        @if($dashRunning)
                            <span x-text="display">0:00</span>
                        @else
                            {{ $todayEntry ? $todayEntry->formatted_hours : '0:00' }}
                        @endif
                        <span class="text-sm font-normal text-gray-500">{{ __('app.hours') }}</span>
                    </p>
                </div>
            </div>
            <div class="mt-4">
                @if($todayEntry && $todayEntry->start_time && !$todayEntry->end_time)
                    <span class="inline-flex items-center gap-x-1.5 rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-600 animate-pulse"></span>
                        {{ __('app.clock_in') }}: {{ $todayEntry->start_time }}
                    </span>
                @elseif($todayEntry && $todayEntry->start_time)
                    <span class="inline-flex items-center gap-x-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                        {{ $todayEntry->start_time }} - {{ $todayEntry->end_time }}
                    </span>
                @else
                    <a href="{{ route('time-tracking.index') }}" class="inline-flex items-center gap-x-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 hover:bg-gray-200 transition-colors">
                        {{ __('app.clock_in') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Pending Requests --}}
        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center gap-x-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-50">
                    <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('app.pending_requests') }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $pendingRequests }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('absences.team', ['status' => 'pending']) }}" class="text-xs font-medium text-blue-600 hover:text-blue-500">
                    {{ app()->getLocale() === 'de' ? 'Alle anzeigen' : 'View all' }} &rarr;
                </a>
            </div>
        </div>

        {{-- Team Absences Today --}}
        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center gap-x-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-50">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('app.team_absences_today') }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $todayAbsences->count() }}</p>
                </div>
            </div>
            <div class="mt-4">
                @if($todayAbsences->count() > 0)
                    <div class="flex -space-x-2">
                        @foreach($todayAbsences->take(5) as $absence)
                        <div class="h-7 w-7 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 ring-2 ring-white flex items-center justify-center text-white text-xs font-bold" title="{{ $absence->user->name }}">
                            {{ strtoupper(substr($absence->user->name, 0, 1)) }}
                        </div>
                        @endforeach
                        @if($todayAbsences->count() > 5)
                        <div class="h-7 w-7 rounded-full bg-gray-200 ring-2 ring-white flex items-center justify-center text-gray-600 text-xs font-bold">
                            +{{ $todayAbsences->count() - 5 }}
                        </div>
                        @endif
                    </div>
                @else
                    <span class="text-xs text-gray-500">{{ __('app.no_data') }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Recent Absence Requests --}}
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">{{ __('app.absences') }}</h2>
                    <a href="{{ route('absences.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">View all</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentRequests as $request)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="flex items-center gap-x-4">
                            <div class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $request->absenceType->color ?? '#6b7280' }}"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $request->absenceType->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $request->start_date->format('d M') }} - {{ $request->end_date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            {{ $request->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $request->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $request->status === 'cancelled' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ __('app.' . $request->status) }}
                        </span>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">{{ __('app.no_data') }}</p>
                        <a href="{{ route('absences.create') }}" class="mt-3 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                            {{ __('app.new_absence_request') }} &rarr;
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Quick Actions & Today's Team --}}
        <div class="space-y-6">
            {{-- Quick Actions --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('time-tracking.index') }}" class="flex items-center gap-x-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition-colors">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('app.clock_in') }}</p>
                            <p class="text-xs text-gray-500">{{ __('app.time_tracking') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('absences.create') }}" class="flex items-center gap-x-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition-colors">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('app.new_absence_request') }}</p>
                            <p class="text-xs text-gray-500">{{ __('app.vacation') }} / {{ __('app.sick_leave') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('calendar') }}" class="flex items-center gap-x-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition-colors">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                            <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('app.calendar') }}</p>
                            <p class="text-xs text-gray-500">{{ __('app.team_absences_today') }}</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Team Absences Today --}}
            @if($todayAbsences->count() > 0)
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('app.team_absences_today') }}</h2>
                <div class="space-y-3">
                    @foreach($todayAbsences->take(5) as $absence)
                    <div class="flex items-center gap-x-3">
                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($absence->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $absence->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $absence->absenceType->name ?? '' }}</p>
                        </div>
                        <div class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $absence->absenceType->color ?? '#6b7280' }}"></div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
