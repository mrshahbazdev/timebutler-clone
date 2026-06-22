<x-guest-layout>
    @section('pageTitle', app()->getLocale() === 'de' ? 'Passwort vergessen' : 'Forgot Password')

    <div class="space-y-8">
        {{-- Header --}}
        <div>
            <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center mb-5">
                <svg class="h-7 w-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
                {{ app()->getLocale() === 'de' ? 'Passwort vergessen?' : 'Forgot your password?' }}
            </h2>
            <p class="mt-2 text-gray-500">
                {{ app()->getLocale() === 'de'
                    ? 'Kein Problem. Geben Sie Ihre E-Mail-Adresse ein und wir senden Ihnen einen Link zum Zurücksetzen.'
                    : 'No worries. Enter your email and we\'ll send you a reset link.' }}
            </p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="rounded-xl bg-green-50 border border-green-200 p-4">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-700">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    {{ __('Email') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="input-custom block w-full rounded-xl border border-gray-300 pl-11 pr-4 py-3 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           placeholder="{{ app()->getLocale() === 'de' ? 'name@firma.de' : 'name@company.com' }}"
                           required autofocus>
                </div>
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-sm">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
                {{ app()->getLocale() === 'de' ? 'Link senden' : 'Send Reset Link' }}
            </button>
        </form>

        {{-- Back to login --}}
        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            {{ app()->getLocale() === 'de' ? 'Zurück zur Anmeldung' : 'Back to sign in' }}
        </a>
    </div>
</x-guest-layout>
