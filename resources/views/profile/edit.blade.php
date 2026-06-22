@extends('layouts.app')

@section('title', __('app.my_profile'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('app.my_profile') }}</h1>

    {{-- Profile Information --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <h2 class="text-lg font-semibold text-gray-900">{{ app()->getLocale() === 'de' ? 'Profilinformationen' : 'Profile Information' }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ app()->getLocale() === 'de' ? 'Aktualisiere deinen Namen und deine E-Mail-Adresse.' : "Update your account's profile information and email address." }}</p>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.name') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-800">
                        {{ app()->getLocale() === 'de' ? 'Deine E-Mail-Adresse ist nicht verifiziert.' : 'Your email address is unverified.' }}
                        <button form="send-verification" class="underline text-sm text-blue-600 hover:text-blue-500">
                            {{ app()->getLocale() === 'de' ? 'Verifizierungs-E-Mail erneut senden' : 'Click here to re-send the verification email.' }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm font-medium text-green-600">
                        {{ app()->getLocale() === 'de' ? 'Ein neuer Verifizierungslink wurde gesendet.' : 'A new verification link has been sent to your email address.' }}
                    </p>
                    @endif
                </div>
                @endif
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                    {{ app()->getLocale() === 'de' ? 'Speichern' : 'Save' }}
                </button>
                @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-600 font-medium">
                    {{ app()->getLocale() === 'de' ? 'Gespeichert.' : 'Saved.' }}
                </p>
                @endif
            </div>
        </form>
    </div>

    {{-- Update Password --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <h2 class="text-lg font-semibold text-gray-900">{{ app()->getLocale() === 'de' ? 'Passwort ändern' : 'Update Password' }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ app()->getLocale() === 'de' ? 'Verwende ein langes, zufälliges Passwort für mehr Sicherheit.' : 'Ensure your account is using a long, random password to stay secure.' }}</p>

        <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale() === 'de' ? 'Aktuelles Passwort' : 'Current Password' }}</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('current_password', 'updatePassword')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale() === 'de' ? 'Neues Passwort' : 'New Password' }}</label>
                <input id="password" name="password" type="password" autocomplete="new-password"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('password', 'updatePassword')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale() === 'de' ? 'Passwort bestätigen' : 'Confirm Password' }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('password_confirmation', 'updatePassword')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                    {{ app()->getLocale() === 'de' ? 'Passwort ändern' : 'Update Password' }}
                </button>
                @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-600 font-medium">
                    {{ app()->getLocale() === 'de' ? 'Gespeichert.' : 'Saved.' }}
                </p>
                @endif
            </div>
        </form>
    </div>

    {{-- Delete Account --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6" x-data="{ showDelete: false }">
        <h2 class="text-lg font-semibold text-gray-900">{{ app()->getLocale() === 'de' ? 'Konto löschen' : 'Delete Account' }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ app()->getLocale() === 'de' ? 'Nach dem Löschen deines Kontos werden alle Daten unwiderruflich gelöscht.' : 'Once your account is deleted, all of its resources and data will be permanently deleted.' }}</p>

        <button @click="showDelete = true" class="mt-4 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors">
            {{ app()->getLocale() === 'de' ? 'Konto löschen' : 'Delete Account' }}
        </button>

        <div x-show="showDelete" x-cloak class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <p class="text-sm text-gray-700 mb-4">{{ app()->getLocale() === 'de' ? 'Bitte gib dein Passwort ein, um dein Konto dauerhaft zu löschen.' : 'Please enter your password to confirm you would like to permanently delete your account.' }}</p>

                <div class="mb-4">
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale() === 'de' ? 'Passwort' : 'Password' }}</label>
                    <input id="delete_password" name="password" type="password" placeholder="{{ app()->getLocale() === 'de' ? 'Passwort' : 'Password' }}"
                           class="block w-full max-w-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    @error('password', 'userDeletion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="showDelete = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        {{ __('app.cancel') }}
                    </button>
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors">
                        {{ app()->getLocale() === 'de' ? 'Konto dauerhaft löschen' : 'Delete Account Permanently' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
