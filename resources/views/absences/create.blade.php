@extends('layouts.app')

@section('title', __('app.new_absence_request'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('absences.index') }}" class="inline-flex items-center gap-x-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            {{ __('app.back') }}
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ $requestType === 'blocked' ? __('app.block_vacation') : __('app.new_absence_request') }}</h1>
        @if($requestType === 'blocked')
        <p class="mt-1 text-sm text-amber-600">{{ __('app.block_vacation_hint') }}</p>
        @endif
    </div>

    <form action="{{ route('absences.store') }}" method="POST" class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6 space-y-6">
        @csrf
        <input type="hidden" name="request_type" value="{{ $requestType }}">

        {{-- Absence Type --}}
        <div>
            <label for="absence_type_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.absence_type') }} *</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($absenceTypes as $type)
                <label class="relative flex cursor-pointer rounded-lg border p-4 hover:border-blue-300 transition-colors">
                    <input type="radio" name="absence_type_id" value="{{ $type->id }}" class="peer sr-only" {{ old('absence_type_id') == $type->id ? 'checked' : '' }}>
                    <div class="flex items-center gap-x-3 w-full">
                        <div class="h-3 w-3 rounded-full shrink-0" style="background-color: {{ $type->color }}"></div>
                        <span class="text-sm font-medium text-gray-900">{{ $type->name }}</span>
                    </div>
                    <div class="absolute inset-0 rounded-lg border-2 border-transparent peer-checked:border-blue-500 pointer-events-none"></div>
                </label>
                @endforeach
            </div>
            @error('absence_type_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Date Range --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.start_date') }} *</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <label class="mt-2 inline-flex items-center gap-x-2">
                    <input type="checkbox" name="half_day_start" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-600">{{ __('app.half_day') }}</span>
                </label>
                @error('start_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.end_date') }} *</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <label class="mt-2 inline-flex items-center gap-x-2">
                    <input type="checkbox" name="half_day_end" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-600">{{ __('app.half_day') }}</span>
                </label>
                @error('end_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Substitute --}}
        <div>
            <label for="substitute_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.substitute') }}</label>
            <select name="substitute_id" id="substitute_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">-- {{ __('app.substitute') }} --</option>
                @foreach($colleagues as $colleague)
                <option value="{{ $colleague->id }}">{{ $colleague->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Notes --}}
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.notes') }}</label>
            <textarea name="notes" id="notes" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="{{ __('app.notes') }}...">{{ old('notes') }}</textarea>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('absences.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors">{{ __('app.cancel') }}</a>
            <button type="submit" class="rounded-lg {{ $requestType === 'blocked' ? 'bg-amber-600 hover:bg-amber-500' : 'bg-blue-600 hover:bg-blue-500' }} px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors">
                {{ $requestType === 'blocked' ? __('app.block_vacation') : __('app.submit_request') }}
            </button>
        </div>
    </form>
</div>
@endsection
