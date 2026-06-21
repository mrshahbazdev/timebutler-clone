@extends('layouts.app')

@section('title', __('app.team_requests'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.team_requests') }}</h1>
        <div class="flex items-center gap-x-2">
            <a href="{{ route('absences.team', ['status' => 'pending']) }}" class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ __('app.pending') }}
            </a>
            <a href="{{ route('absences.team', ['status' => 'approved']) }}" class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') === 'approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ __('app.approved') }}
            </a>
            <a href="{{ route('absences.team') }}" class="rounded-lg px-3 py-1.5 text-sm font-medium {{ !request('status') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ __('app.all') }}
            </a>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.employee_name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.absence_type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.start_date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.end_date') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.days') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($absences as $absence)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-x-2">
                            <div class="h-7 w-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($absence->user->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $absence->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center gap-x-1.5 text-sm">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $absence->absenceType->color ?? '#6b7280' }}"></span>
                            {{ $absence->absenceType->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $absence->start_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $absence->end_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-3 text-sm text-center font-medium">{{ $absence->total_days }}</td>
                    <td class="px-6 py-3 text-center">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $absence->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $absence->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $absence->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $absence->status === 'cancelled' ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ __('app.' . $absence->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-right">
                        <div class="flex items-center justify-end gap-x-2">
                            @if($absence->status === 'pending')
                            <form action="{{ route('absences.approve', $absence) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="rounded-lg bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 transition-colors">
                                    {{ __('app.approve') }}
                                </button>
                            </form>
                            <form action="{{ route('absences.reject', $absence) }}" method="POST" class="inline" x-data="{ showReason: false }">
                                @csrf
                                <button type="button" @click="showReason = !showReason" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors">
                                    {{ __('app.reject') }}
                                </button>
                                <div x-show="showReason" class="mt-2 flex gap-2">
                                    <input type="text" name="reason" placeholder="{{ __('app.rejection_reason') }}" class="rounded-lg border-gray-300 text-xs w-40">
                                    <button type="submit" class="rounded-lg bg-red-600 px-2 py-1 text-xs text-white">OK</button>
                                </div>
                            </form>
                            @endif
                            @if(in_array($absence->status, ['approved', 'rejected']))
                            <a href="{{ route('absences.decision-pdf', $absence) }}" class="text-gray-400 hover:text-gray-600" title="{{ __('app.print') }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.75 3.75h-1.5" />
                                </svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('app.no_data') }}</td>
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
