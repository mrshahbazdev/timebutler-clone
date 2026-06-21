@extends('layouts.app')

@section('title', __('app.settings'))

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('app.settings') }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Language Settings --}}
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('app.language') }}</h2>
                <p class="text-sm text-gray-500 mb-4">{{ __('app.switch_language') }}</p>

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

        {{-- Current Info --}}
        <div>
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('app.info') ?? 'Info' }}</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">{{ __('app.language') }}</dt>
                        <dd class="text-sm text-gray-900">{{ app()->getLocale() === 'de' ? 'Deutsch' : 'English' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Version</dt>
                        <dd class="text-sm text-gray-900">1.0.0</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Timezone</dt>
                        <dd class="text-sm text-gray-900">Europe/Berlin</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
