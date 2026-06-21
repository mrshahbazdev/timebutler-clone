<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('app.register_company') }}</h2>
        <p class="mt-1 text-sm text-gray-500">TimeButler</p>
    </div>

    <form method="POST" action="{{ route('register-company.store') }}" class="space-y-4">
        @csrf

        <div class="rounded-lg bg-gray-50 p-4 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">{{ __('app.company_name') }}</h3>

            <div>
                <x-input-label for="company_name" :value="__('app.company_name')" />
                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autofocus />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="federal_state" :value="__('app.federal_state')" />
                <select id="federal_state" name="federal_state" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($states as $code => $name)
                    <option value="{{ $code }}" {{ old('federal_state', 'NW') === $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('federal_state')" class="mt-2" />
            </div>
        </div>

        <div class="rounded-lg bg-gray-50 p-4 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Administrator</h3>

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            </div>
        </div>

        <div>
            <button type="submit" class="w-full flex justify-center rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                {{ __('app.create_account') }}
            </button>
        </div>

        <p class="text-center text-sm text-gray-500">
            {{ __('app.already_registered') }}
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">{{ __('Login') }}</a>
        </p>
    </form>
</x-guest-layout>
