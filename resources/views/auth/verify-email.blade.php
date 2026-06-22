<x-guest-layout>
    @section('pageTitle', app()->getLocale() === 'de' ? 'E-Mail bestätigen' : 'Verify Email')

    <div class="space-y-8">
        {{-- Header --}}
        <div class="text-center">
            <div class="mx-auto h-16 w-16 rounded-2xl bg-blue-100 flex items-center justify-center mb-5">
                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04a2.25 2.25 0 011.183 1.98V18z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
                {{ app()->getLocale() === 'de' ? 'E-Mail bestätigen' : 'Check your email' }}
            </h2>
            <p class="mt-3 text-gray-500 max-w-sm mx-auto">
                {{ app()->getLocale() === 'de'
                    ? 'Danke für die Registrierung! Bitte bestätigen Sie Ihre E-Mail-Adresse über den Link, den wir Ihnen gesendet haben.'
                    : 'Thanks for signing up! Please verify your email address by clicking the link we just sent you.' }}
            </p>
        </div>

        {{-- Success message --}}
        @if (session('status') == 'verification-link-sent')
            <div class="rounded-xl bg-green-50 border border-green-200 p-4">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-700">
                        {{ app()->getLocale() === 'de'
                            ? 'Ein neuer Bestätigungslink wurde an Ihre E-Mail-Adresse gesendet.'
                            : 'A new verification link has been sent to your email address.' }}
                    </p>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-sm">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    {{ app()->getLocale() === 'de' ? 'Bestätigungsmail erneut senden' : 'Resend Verification Email' }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all">
                    <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    {{ app()->getLocale() === 'de' ? 'Abmelden' : 'Log Out' }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
