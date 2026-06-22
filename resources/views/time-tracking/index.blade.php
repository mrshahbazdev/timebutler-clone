@extends('layouts.app')

@section('title', __('app.time_tracking'))

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('app.time_tracking') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ now()->translatedFormat('l, d. F Y') }}</p>
        </div>
    </div>

    {{-- Clock In/Out Card --}}
    @php
        $isRunning = $todayEntry && $todayEntry->start_time && !$todayEntry->end_time;
        $dailyTarget = auth()->user()->weekly_hours / 5;
        $targetMinutes = $dailyTarget * 60;
    @endphp
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6"
         @if($isRunning)
         x-data="{
            startTime: new Date('{{ now()->format('Y-m-d') }}T{{ $todayEntry->start_time }}'),
            elapsed: 0,
            target: {{ $targetMinutes }},
            hours: '0',
            minutes: '00',
            seconds: '00',
            percent: 0,
            init() {
                this.tick();
                setInterval(() => this.tick(), 1000);
            },
            tick() {
                this.elapsed = Math.floor((Date.now() - this.startTime.getTime()) / 1000);
                let totalMin = Math.floor(this.elapsed / 60);
                this.hours = String(Math.floor(totalMin / 60));
                this.minutes = String(totalMin % 60).padStart(2, '0');
                this.seconds = String(this.elapsed % 60).padStart(2, '0');
                this.percent = Math.min(100, (totalMin / Math.max(this.target, 1)) * 100);
            }
         }"
         @elseif($todayEntry && $todayEntry->end_time)
         x-data="{
            hours: '{{ intdiv($todayEntry->total_minutes ?? 0, 60) }}',
            minutes: '{{ str_pad(($todayEntry->total_minutes ?? 0) % 60, 2, '0', STR_PAD_LEFT) }}',
            seconds: '00',
            percent: {{ min(100, (($todayEntry->total_minutes ?? 0) / max($targetMinutes, 1)) * 100) }}
         }"
         @else
         x-data="{ hours: '0', minutes: '00', seconds: '00', percent: 0 }"
         @endif
    >
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('app.hours_today') }}</h2>
                <p class="mt-1 text-3xl font-bold text-gray-900">
                    <span x-text="hours + ':' + minutes"></span><span class="text-xl text-gray-400" x-text="':' + seconds"></span>
                </p>
                @if($todayEntry && $todayEntry->start_time)
                <p class="mt-1 text-sm text-gray-500">{{ __('app.start_time') }}: {{ $todayEntry->start_time }}</p>
                @endif
            </div>
            <div class="flex gap-3">
                @if(!$todayEntry || !$todayEntry->start_time)
                <form action="{{ route('time-tracking.clock-in') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-x-2 rounded-lg bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                        </svg>
                        {{ __('app.clock_in') }}
                    </button>
                </form>
                @elseif(!$todayEntry->end_time)
                <div x-data="{ showBreakPrompt: false }">
                    <button @click="showBreakPrompt = true" type="button" class="inline-flex items-center gap-x-2 rounded-lg bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                        </svg>
                        {{ __('app.clock_out') }}
                    </button>

                    {{-- Break Minutes Modal --}}
                    <div x-show="showBreakPrompt" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50" @keydown.escape.window="showBreakPrompt = false">
                        <div class="rounded-xl bg-white shadow-xl ring-1 ring-gray-900/5 p-6 w-full max-w-sm mx-4" @click.outside="showBreakPrompt = false">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('app.clock_out') }}</h3>
                            <p class="text-sm text-gray-500 mb-4">{{ app()->getLocale() === 'de' ? 'Wie viele Minuten Pause hatten Sie?' : 'How many minutes of break did you take?' }}</p>
                            <form action="{{ route('time-tracking.clock-out') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.break') }} (min)</label>
                                    <input type="number" name="break_minutes" value="30" min="0" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" autofocus>
                                </div>
                                <div class="flex gap-3 justify-end">
                                    <button type="button" @click="showBreakPrompt = false" class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                                        {{ __('app.cancel') }}
                                    </button>
                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors">
                                        {{ __('app.clock_out') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <span class="inline-flex items-center rounded-full bg-gray-100 px-4 py-2 text-sm font-medium text-gray-600">
                    {{ $todayEntry->start_time }} - {{ $todayEntry->end_time }}
                </span>
                @endif
            </div>
        </div>

        {{-- Timer bar --}}
        @if($isRunning)
        <div class="mt-4">
            <div class="flex items-center gap-x-2">
                <span class="h-2.5 w-2.5 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-sm font-medium text-green-700">Recording...</span>
            </div>
            <div class="mt-2 h-2 w-full rounded-full bg-gray-100">
                <div class="h-2 rounded-full bg-green-500 transition-all duration-1000" :style="'width: ' + percent + '%'"></div>
            </div>
            <p class="mt-1 text-xs text-gray-500"><span x-text="Math.round(percent)">0</span>% of daily target ({{ $dailyTarget }}h)</p>
        </div>
        @elseif($todayEntry && $todayEntry->end_time)
        <div class="mt-4">
            <div class="flex items-center gap-x-2">
                <span class="h-2.5 w-2.5 rounded-full bg-gray-400"></span>
                <span class="text-sm font-medium text-gray-500">{{ app()->getLocale() === 'de' ? 'Abgeschlossen' : 'Completed' }}</span>
            </div>
            <div class="mt-2 h-2 w-full rounded-full bg-gray-100">
                <div class="h-2 rounded-full bg-blue-500" :style="'width: ' + percent + '%'"></div>
            </div>
            <p class="mt-1 text-xs text-gray-500"><span x-text="Math.round(percent)">0</span>% of daily target ({{ $dailyTarget }}h)</p>
        </div>
        @endif
    </div>

    {{-- Manual Entry Form --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6" x-data="{ showForm: false }">
        <button @click="showForm = !showForm" class="flex items-center gap-x-2 text-sm font-medium text-blue-600 hover:text-blue-500">
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-45': showForm }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('app.save_entry') }}
        </button>

        <form x-show="showForm" x-cloak action="{{ route('time-tracking.store') }}" method="POST" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.start_date') }}</label>
                <input type="date" name="date" value="{{ today()->format('Y-m-d') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.start_time') }}</label>
                <input type="time" name="start_time" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.end_time') }}</label>
                <input type="time" name="end_time" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.break') }} (min)</label>
                <input type="number" name="break_minutes" value="30" min="0" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.project') }}</label>
                <input type="text" name="project" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.notes') }}</label>
                <input type="text" name="notes" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div class="sm:col-span-2 lg:col-span-2 flex items-end">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                    {{ __('app.save_entry') }}
                </button>
            </div>
        </form>
    </div>

    {{-- History --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-900">History</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.start_date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.start_time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.end_time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.break') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.total_hours') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.project') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($entries as $entry)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-sm text-gray-900">{{ $entry->date->format('d.m.Y') }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $entry->start_time ?? '-' }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $entry->end_time ?? '-' }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $entry->break_minutes }} min</td>
                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $entry->formatted_hours }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $entry->project ?? '-' }}</td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $entry->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $entry->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                            {{ $entry->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ __('app.' . $entry->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-right">
                        @if($entry->status !== 'approved')
                        <a href="{{ route('time-tracking.edit', $entry) }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            {{ __('app.edit') }}
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('app.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($entries->hasPages())
        <div class="border-t border-gray-200 px-6 py-3">
            {{ $entries->links() }}
        </div>
        @endif
    </div>

    {{-- Edit Entry Modal --}}
    @if(isset($editEntry))
    <div x-data="{ open: true }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50" @keydown.escape.window="open = false; window.location = '{{ route('time-tracking.index') }}'">
        <div class="rounded-xl bg-white shadow-xl ring-1 ring-gray-900/5 p-6 w-full max-w-lg mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('app.edit') }} — {{ $editEntry->date->format('d.m.Y') }}</h3>
            <p class="text-sm text-gray-500 mb-4">{{ app()->getLocale() === 'de' ? 'Zeiten und Pause anpassen' : 'Adjust times and break' }}</p>
            <form action="{{ route('time-tracking.update', $editEntry) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.start_time') }}</label>
                        <input type="time" name="start_time" value="{{ $editEntry->start_time }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.end_time') }}</label>
                        <input type="time" name="end_time" value="{{ $editEntry->end_time }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.break') }} (min)</label>
                    <input type="number" name="break_minutes" value="{{ $editEntry->break_minutes }}" min="0" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.project') }}</label>
                    <input type="text" name="project" value="{{ $editEntry->project }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.notes') }}</label>
                    <input type="text" name="notes" value="{{ $editEntry->notes }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <a href="{{ route('time-tracking.index') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                        {{ __('app.cancel') }}
                    </a>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                        {{ __('app.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
