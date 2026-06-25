@extends('layouts.app')

@section('title', __('app.absences'))

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('app.absences') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('app.showing') }} {{ $absences->total() }} {{ __('app.results') }}</p>
        </div>
        <div class="flex items-center gap-x-2">
            <a href="{{ route('absences.create', ['type' => 'request']) }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('app.request_vacation') }}
            </a>
            <a href="{{ route('absences.create', ['type' => 'blocked']) }}" class="inline-flex items-center gap-x-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                {{ __('app.block_vacation') }}
            </a>
        </div>
    </div>

    {{-- Vacation Balance Card --}}
    @if($vacationBalance)
    <div class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white shadow-lg">
        <h2 class="text-sm font-medium text-blue-100">{{ __('app.my_vacation_balance') }} {{ now()->year }}</h2>
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-6">
            <div>
                <p class="text-3xl font-bold">{{ $vacationBalance->total_days }}</p>
                <p class="text-sm text-blue-200">{{ __('app.total_days') }}</p>
            </div>
            <div>
                <p class="text-3xl font-bold">{{ $vacationBalance->used_days }}</p>
                <p class="text-sm text-blue-200">{{ __('app.days_taken') }}</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-yellow-300">{{ $requestedDays }}</p>
                <p class="text-sm text-blue-200">{{ __('app.days_requested') }}</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-green-300">{{ $vacationBalance->remaining_days }}</p>
                <p class="text-sm text-blue-200">{{ __('app.days_remaining') }}</p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-blue-200 mb-1">
                <span>{{ __('app.used') }}</span>
                <span>{{ round(($vacationBalance->used_days / max($vacationBalance->total_days, 1)) * 100) }}%</span>
            </div>
            <div class="h-2 rounded-full bg-blue-800/50">
                <div class="h-2 rounded-full bg-white/80" style="width: {{ min(($vacationBalance->used_days / max($vacationBalance->total_days, 1)) * 100, 100) }}%"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="flex gap-2">
        <a href="{{ route('absences.index') }}" class="rounded-full px-4 py-1.5 text-sm font-medium {{ !request('status') ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">{{ __('app.all') }}</a>
        <a href="{{ route('absences.index', ['status' => 'pending']) }}" class="rounded-full px-4 py-1.5 text-sm font-medium {{ request('status') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">{{ __('app.pending') }}</a>
        <a href="{{ route('absences.index', ['status' => 'approved']) }}" class="rounded-full px-4 py-1.5 text-sm font-medium {{ request('status') === 'approved' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">{{ __('app.approved') }}</a>
        <a href="{{ route('absences.index', ['status' => 'rejected']) }}" class="rounded-full px-4 py-1.5 text-sm font-medium {{ request('status') === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">{{ __('app.rejected') }}</a>
    </div>

    {{-- Table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.absence_type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.start_date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.end_date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.days') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($absences as $absence)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-x-3">
                            <div class="h-3 w-3 rounded-full" style="background-color: {{ $absence->absenceType->color ?? '#6b7280' }}"></div>
                            <span class="text-sm font-medium text-gray-900">{{ $absence->absenceType->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $absence->start_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $absence->end_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $absence->total_days }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $absence->request_type === 'blocked' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $absence->request_type === 'blocked' ? __('app.blocked') : __('app.requested') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            {{ $absence->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $absence->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $absence->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $absence->status === 'cancelled' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ __('app.' . $absence->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        @if(in_array($absence->status, ['pending', 'approved']))
                        @if($absence->request_type === 'blocked' && $absence->status === 'pending')
                        <form action="{{ route('absences.convert', $absence) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-blue-500 transition-colors mr-2" title="{{ __('app.request_vacation') }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('absences.cancel', $absence) }}" method="POST" onsubmit="return confirm('{{ __('app.confirm_cancel') }}')" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="{{ __('app.cancel') }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">{{ __('app.no_data') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($absences->hasPages())
        <div class="border-t border-gray-200 px-6 py-3">{{ $absences->links() }}</div>
        @endif
    </div>
</div>
@endsection
