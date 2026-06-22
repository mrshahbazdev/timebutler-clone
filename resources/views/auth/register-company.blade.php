<x-guest-layout>
    @section('pageTitle', app()->getLocale() === 'de' ? 'Firma registrieren' : 'Register Company')

    <div class="space-y-8">
        {{-- Header --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
                {{ app()->getLocale() === 'de' ? 'Firma registrieren' : 'Register your company' }}
            </h2>
            <p class="mt-2 text-gray-500">
                {{ app()->getLocale() === 'de'
                    ? 'Erstellen Sie Ihr Firmenkonto und legen Sie los'
                    : 'Set up your company account and get started' }}
            </p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('register-company.store') }}" class="space-y-6">
            @csrf

            {{-- Company section --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 space-y-4 shadow-sm">
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="h-4.5 w-4.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">
                        {{ app()->getLocale() === 'de' ? 'Firmendaten' : 'Company Details' }}
                    </h3>
                </div>

                <div>
                    <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        {{ __('app.company_name') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        </div>
                        <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}"
                               class="input-custom block w-full rounded-xl border border-gray-300 pl-11 pr-4 py-3 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               placeholder="{{ app()->getLocale() === 'de' ? 'z.B. Muster GmbH' : 'e.g. Acme Inc.' }}"
                               required autofocus>
                    </div>
                    @error('company_name')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="federal_state" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        {{ __('app.federal_state') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <select id="federal_state" name="federal_state"
                                class="input-custom block w-full rounded-xl border border-gray-300 pl-11 pr-4 py-3 text-gray-900 focus:border-blue-500 focus:ring-blue-500 sm:text-sm appearance-none bg-white">
                            @foreach($states as $code => $name)
                                <option value="{{ $code }}" {{ old('federal_state', 'NW') === $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('federal_state')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Admin section --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 space-y-4 shadow-sm">
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="h-4.5 w-4.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Administrator</h3>
                </div>

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Name') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                               class="input-custom block w-full rounded-xl border border-gray-300 pl-11 pr-4 py-3 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               placeholder="{{ app()->getLocale() === 'de' ? 'Vollständiger Name' : 'Full name' }}"
                               required>
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Email') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="input-custom block w-full rounded-xl border border-gray-300 pl-11 pr-4 py-3 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               placeholder="{{ app()->getLocale() === 'de' ? 'admin@firma.de' : 'admin@company.com' }}"
                               required>
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <input id="password" type="password" name="password"
                                   class="input-custom block w-full rounded-xl border border-gray-300 pl-11 pr-4 py-3 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   placeholder="••••••••"
                                   required>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            {{ app()->getLocale() === 'de' ? 'Bestätigen' : 'Confirm' }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   class="input-custom block w-full rounded-xl border border-gray-300 pl-11 pr-4 py-3 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   placeholder="••••••••"
                                   required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2 rounded-xl px-4 py-3.5 text-sm font-semibold text-white shadow-sm">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                </svg>
                {{ __('app.create_account') }}
            </button>
        </form>

        {{-- Login link --}}
        <p class="text-center text-sm text-gray-500">
            {{ __('app.already_registered') }}
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-500 transition-colors">
                {{ app()->getLocale() === 'de' ? 'Jetzt anmelden' : 'Sign in' }}
            </a>
        </p>
    </div>
</x-guest-layout>
