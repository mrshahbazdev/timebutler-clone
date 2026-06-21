@extends('layouts.app')

@section('title', 'Add Department')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('departments.index') }}" class="inline-flex items-center gap-x-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            {{ __('app.back') }}
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Add Department</h1>
    </div>

    <form action="{{ route('departments.store') }}" method="POST" class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6 space-y-6">
        @csrf
        @include('departments._form')

        <div class="flex items-center justify-end gap-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('departments.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors">{{ __('app.cancel') }}</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">{{ __('app.save') }}</button>
        </div>
    </form>
</div>
@endsection
