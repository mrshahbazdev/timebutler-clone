@extends('layouts.app')

@section('title', __('app.settings'))

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('app.settings') }}</h1>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 p-4">
        <div class="flex items-center gap-x-2">
            <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Organization Settings --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Timezone & Work Settings --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">{{ app()->getLocale() === 'de' ? 'Unternehmenseinstellungen' : 'Company Settings' }}</h2>
                        <p class="text-sm text-gray-500">{{ app()->getLocale() === 'de' ? 'Zeitzone, Arbeitsstunden und Urlaubstage konfigurieren' : 'Configure timezone, work hours and vacation days' }}</p>
                    </div>
                </div>

                <form action="{{ route('settings.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Timezone --}}
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ app()->getLocale() === 'de' ? 'Zeitzone' : 'Timezone' }}
                        </label>
                        @php
                            $commonTimezones = [
                                'Europe/Berlin' => 'Berlin (CET/CEST) - Deutschland',
                                'Europe/Vienna' => 'Wien (CET/CEST) - Österreich',
                                'Europe/Zurich' => 'Zürich (CET/CEST) - Schweiz',
                                'Europe/Amsterdam' => 'Amsterdam (CET/CEST)',
                                'Europe/Brussels' => 'Brüssel (CET/CEST)',
                                'Europe/Paris' => 'Paris (CET/CEST)',
                                'Europe/London' => 'London (GMT/BST)',
                                'Europe/Madrid' => 'Madrid (CET/CEST)',
                                'Europe/Rome' => 'Rom (CET/CEST)',
                                'Europe/Warsaw' => 'Warschau (CET/CEST)',
                                'Europe/Prague' => 'Prag (CET/CEST)',
                                'Europe/Stockholm' => 'Stockholm (CET/CEST)',
                                'Europe/Helsinki' => 'Helsinki (EET/EEST)',
                                'Europe/Athens' => 'Athen (EET/EEST)',
                                'Europe/Istanbul' => 'Istanbul (TRT)',
                                'Europe/Moscow' => 'Moskau (MSK)',
                                'America/New_York' => 'New York (EST/EDT)',
                                'America/Chicago' => 'Chicago (CST/CDT)',
                                'America/Denver' => 'Denver (MST/MDT)',
                                'America/Los_Angeles' => 'Los Angeles (PST/PDT)',
                                'Asia/Dubai' => 'Dubai (GST)',
                                'Asia/Kolkata' => 'Kolkata (IST)',
                                'Asia/Shanghai' => 'Shanghai (CST)',
                                'Asia/Tokyo' => 'Tokio (JST)',
                                'Australia/Sydney' => 'Sydney (AEST/AEDT)',
                                'Pacific/Auckland' => 'Auckland (NZST/NZDT)',
                                'UTC' => 'UTC',
                            ];
                        @endphp
                        <select id="timezone" name="timezone"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @foreach($commonTimezones as $tz => $label)
                            <option value="{{ $tz }}" {{ ($organization->timezone ?? 'Europe/Berlin') === $tz ? 'selected' : '' }}>
                                {{ $label }} ({{ now()->setTimezone($tz)->format('H:i') }})
                            </option>
                            @endforeach
                        </select>
                        @error('timezone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-400">
                            {{ app()->getLocale() === 'de' ? 'Aktuelle Uhrzeit wird in Klammern angezeigt' : 'Current time shown in brackets' }}
                        </p>
                    </div>

                    {{-- Work Hours + Vacation Days --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="work_hours_per_day" class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ app()->getLocale() === 'de' ? 'Arbeitsstunden pro Tag' : 'Work Hours per Day' }}
                            </label>
                            <div class="relative">
                                <input id="work_hours_per_day" name="work_hours_per_day" type="number" step="0.5" min="1" max="24"
                                       value="{{ old('work_hours_per_day', $organization->work_hours_per_day ?? 8) }}"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pr-10">
                                <span class="absolute inset-y-0 right-3 flex items-center text-xs text-gray-400">h</span>
                            </div>
                            @error('work_hours_per_day')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="default_vacation_days" class="block text-sm font-medium text-gray-700 mb-1.5">
                                {{ app()->getLocale() === 'de' ? 'Standard-Urlaubstage' : 'Default Vacation Days' }}
                            </label>
                            <div class="relative">
                                <input id="default_vacation_days" name="default_vacation_days" type="number" min="0" max="365"
                                       value="{{ old('default_vacation_days', $organization->default_vacation_days ?? 30) }}"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pr-14">
                                <span class="absolute inset-y-0 right-3 flex items-center text-xs text-gray-400">{{ app()->getLocale() === 'de' ? 'Tage' : 'days' }}</span>
                            </div>
                            @error('default_vacation_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                            {{ app()->getLocale() === 'de' ? 'Einstellungen speichern' : 'Save Settings' }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Language Settings --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">{{ __('app.language') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('app.switch_language') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('locale.switch', 'de') }}"
                       class="relative flex items-center gap-x-4 rounded-lg border-2 p-4 transition-colors
                              {{ app()->getLocale() === 'de' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <span class="text-3xl">🇩🇪</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Deutsch</p>
                            <p class="text-xs text-gray-500">German</p>
                        </div>
                        @if(app()->getLocale() === 'de')
                        <svg class="absolute top-3 right-3 h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        @endif
                    </a>
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="relative flex items-center gap-x-4 rounded-lg border-2 p-4 transition-colors
                              {{ app()->getLocale() === 'en' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <span class="text-3xl">🇬🇧</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">English</p>
                            <p class="text-xs text-gray-500">English</p>
                        </div>
                        @if(app()->getLocale() === 'en')
                        <svg class="absolute top-3 right-3 h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        {{-- Info Sidebar --}}
        <div class="space-y-6">
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('app.info') ?? 'Info' }}</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">{{ app()->getLocale() === 'de' ? 'Unternehmen' : 'Company' }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $organization->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">{{ app()->getLocale() === 'de' ? 'Bundesland' : 'Federal State' }}</dt>
                        <dd class="text-sm text-gray-900">{{ $organization->federal_state ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">{{ app()->getLocale() === 'de' ? 'Zeitzone' : 'Timezone' }}</dt>
                        <dd class="text-sm text-gray-900">{{ $organization->timezone ?? 'Europe/Berlin' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">{{ app()->getLocale() === 'de' ? 'Aktuelle Uhrzeit' : 'Current Time' }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ now()->setTimezone($organization->timezone ?? 'Europe/Berlin')->format('H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">{{ __('app.language') }}</dt>
                        <dd class="text-sm text-gray-900">{{ app()->getLocale() === 'de' ? 'Deutsch' : 'English' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Version</dt>
                        <dd class="text-sm text-gray-900">1.0.0</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
