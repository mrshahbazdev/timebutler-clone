@extends('layouts.app')

@section('title', app()->getLocale() === 'de' ? 'Überstunden verwalten' : 'Manage Overtime')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Überstunden verwalten' : 'Manage Overtime' }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ app()->getLocale() === 'de' ? 'Überstunden retroaktiv für Mitarbeiter eintragen oder anpassen' : 'Enter or adjust overtime retroactively for employees' }}</p>
        </div>
    </div>

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

    {{-- Employee & Year Selection --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <form action="{{ route('overtime.admin') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.employees') }}</label>
                <select name="employee_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">{{ app()->getLocale() === 'de' ? '-- Mitarbeiter wählen --' : '-- Select Employee --' }}</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $selectedUserId == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }} {{ $emp->employee_number ? '(#' . $emp->employee_number . ')' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.year') }}</label>
                <select name="year" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    @for($y = now()->year + 1; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                    {{ app()->getLocale() === 'de' ? 'Anzeigen' : 'Show' }}
                </button>
            </div>
        </form>
    </div>

    {{-- Monthly Overtime Grid --}}
    @if($selectedUserId)
    @php
        $selectedEmployee = $employees->firstWhere('id', $selectedUserId);
        $monthNames = app()->getLocale() === 'de'
            ? ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
            : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $totalBalance = $balances->sum('balance_minutes');
        $totalAbs = abs($totalBalance);
        $totalSign = $totalBalance >= 0 ? '+' : '-';
        $totalFormatted = sprintf('%s%d:%02d', $totalSign, intdiv($totalAbs, 60), $totalAbs % 60);
    @endphp
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    {{ $selectedEmployee->name ?? '' }} — {{ $selectedYear }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ app()->getLocale() === 'de' ? 'Gesamtsaldo' : 'Total balance' }}:
                    <span class="font-semibold {{ $totalBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $totalFormatted }}</span>
                </p>
            </div>
        </div>

        <form action="{{ route('overtime.bulk-store') }}" method="POST">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $selectedUserId }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'de' ? 'Monat' : 'Month' }}</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'de' ? 'Automatisch' : 'Auto' }}</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">+/- {{ app()->getLocale() === 'de' ? 'Manuell' : 'Manual' }}</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.hours') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('app.minutes') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'de' ? 'Monatssaldo' : 'Month Total' }}</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'de' ? 'Kumulierter Saldo' : 'Running Total' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @for($m = 1; $m <= 12; $m++)
                        @php
                            $existing = $balances->get($m);
                            $existingAdjustment = $existing ? $existing->adjustment_minutes : 0;
                            $isNeg = $existingAdjustment < 0;
                            $absMin = abs($existingAdjustment);
                            $h = intdiv($absMin, 60);
                            $min = $absMin % 60;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $monthNames[$m - 1] }}
                                <input type="hidden" name="entries[{{ $m - 1 }}][month]" value="{{ $m }}">
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($existing)
                                <span class="text-sm font-medium {{ $existing->calculated_minutes >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $existing->formatted_calculated }}
                                </span>
                                @else
                                <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <label class="inline-flex items-center gap-x-1 cursor-pointer">
                                    <input type="checkbox" name="entries[{{ $m - 1 }}][is_negative]" value="1"
                                           {{ $isNeg ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-xs text-gray-500">{{ app()->getLocale() === 'de' ? 'Minus' : 'Negative' }}</span>
                                </label>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="entries[{{ $m - 1 }}][hours]" value="{{ $h }}" min="0"
                                       class="block w-20 mx-auto rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-center">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="entries[{{ $m - 1 }}][minutes]" value="{{ $min }}" min="0" max="59"
                                       class="block w-20 mx-auto rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-center">
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($existing)
                                <span class="text-sm font-bold {{ $existing->balance_minutes >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $existing->formatted_balance }}
                                </span>
                                @else
                                <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($existing)
                                @php
                                    $rt = $runningTotals[$m] ?? 0;
                                    $rtAbs = abs($rt);
                                    $rtSign = $rt >= 0 ? '+' : '-';
                                    $rtFormatted = sprintf('%s%d:%02d', $rtSign, intdiv($rtAbs, 60), $rtAbs % 60);
                                @endphp
                                <span class="text-sm font-bold {{ $rt >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $rtFormatted }}
                                </span>
                                @else
                                <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    {{ app()->getLocale() === 'de'
                        ? 'Tipp: Tragen Sie die Überstunden für jeden Monat ein und speichern Sie alle auf einmal.'
                        : 'Tip: Enter overtime for each month and save all at once.' }}
                </p>
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                    {{ app()->getLocale() === 'de' ? 'Alle Monate speichern' : 'Save All Months' }}
                </button>
            </div>
        </form>
    </div>

    {{-- Quick Single-Month Adjustment --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">{{ app()->getLocale() === 'de' ? 'Einzelnen Monat anpassen' : 'Adjust Single Month' }}</h2>
        <form action="{{ route('overtime.store-adjustment') }}" method="POST" class="flex flex-wrap items-end gap-4">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $selectedUserId }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ app()->getLocale() === 'de' ? 'Monat' : 'Month' }}</label>
                <select name="month" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ $monthNames[$m - 1] }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">+/-</label>
                <label class="inline-flex items-center gap-x-1 h-[38px] cursor-pointer">
                    <input type="checkbox" name="is_negative" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-xs text-gray-500">{{ app()->getLocale() === 'de' ? 'Minus' : 'Negative' }}</span>
                </label>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.hours') }}</label>
                <input type="number" name="hours" value="0" min="0" class="block w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('app.minutes') }}</label>
                <input type="number" name="minutes" value="0" min="0" max="59" class="block w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <button type="submit" class="rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors">
                    {{ __('app.save') }}
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
