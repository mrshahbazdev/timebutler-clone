@extends('layouts.app')

@section('title', __('app.departments'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.departments') }}</h1>
        <a href="{{ route('departments.create') }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Department
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($departments as $dept)
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-x-3">
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center" style="background-color: {{ $dept->color }}20">
                        <svg class="h-5 w-5" style="color: {{ $dept->color }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $dept->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $dept->employees_count }} {{ __('app.employees') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-x-1">
                    <a href="{{ route('departments.edit', $dept) }}" class="p-1 text-gray-400 hover:text-amber-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                        </svg>
                    </a>
                    <form action="{{ route('departments.destroy', $dept) }}" method="POST" onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @if($dept->manager)
            <div class="mt-4 flex items-center gap-x-2 text-xs text-gray-500">
                <div class="h-6 w-6 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($dept->manager->name, 0, 1)) }}
                </div>
                {{ $dept->manager->name }} ({{ __('app.employee_manager') }})
            </div>
            @endif
        </div>
        @empty
        <div class="sm:col-span-2 lg:col-span-3 rounded-xl bg-white p-12 shadow-sm ring-1 ring-gray-900/5 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">{{ __('app.no_data') }}</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
