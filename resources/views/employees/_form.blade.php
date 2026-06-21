{{-- Shared form for create/edit employee --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    {{-- Name --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.employee_name') }} *</label>
        <input type="text" name="name" id="name" value="{{ old('name', $employee->name ?? '') }}" required
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.employee_email') }} *</label>
        <input type="email" name="email" id="email" value="{{ old('email', $employee->email ?? '') }}" required
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Password --}}
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Password {{ isset($employee) ? '' : '*' }}
        </label>
        <input type="password" name="password" id="password" {{ isset($employee) ? '' : 'required' }}
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
               placeholder="{{ isset($employee) ? 'Leave blank to keep current' : '' }}">
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Password Confirm --}}
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
            Confirm Password {{ isset($employee) ? '' : '*' }}
        </label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
    </div>

    {{-- Department --}}
    <div>
        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.employee_department') }}</label>
        <select name="department_id" id="department_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="">--</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Manager --}}
    <div>
        <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.employee_manager') }}</label>
        <select name="manager_id" id="manager_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="">--</option>
            @foreach($managers as $mgr)
            <option value="{{ $mgr->id }}" {{ old('manager_id', $employee->manager_id ?? '') == $mgr->id ? 'selected' : '' }}>{{ $mgr->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Role --}}
    <div>
        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
        <select name="role" id="role" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            @foreach($roles as $role)
            <option value="{{ $role->name }}" {{ old('role', isset($employee) ? $employee->roles->first()?->name : 'employee') === $role->name ? 'selected' : '' }}>
                {{ ucfirst($role->name) }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Locale --}}
    <div>
        <label for="locale" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.language') }} *</label>
        <select name="locale" id="locale" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="de" {{ old('locale', $employee->locale ?? 'de') === 'de' ? 'selected' : '' }}>Deutsch</option>
            <option value="en" {{ old('locale', $employee->locale ?? 'de') === 'en' ? 'selected' : '' }}>English</option>
        </select>
    </div>

    {{-- Employee Number --}}
    <div>
        <label for="employee_number" class="block text-sm font-medium text-gray-700 mb-1">Employee #</label>
        <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number', $employee->employee_number ?? '') }}"
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
    </div>

    {{-- Position --}}
    <div>
        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.employee_position') }}</label>
        <input type="text" name="position" id="position" value="{{ old('position', $employee->position ?? '') }}"
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
    </div>

    {{-- Phone --}}
    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone ?? '') }}"
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
    </div>

    {{-- Weekly Hours --}}
    <div>
        <label for="weekly_hours" class="block text-sm font-medium text-gray-700 mb-1">Weekly Hours *</label>
        <input type="number" name="weekly_hours" id="weekly_hours" value="{{ old('weekly_hours', $employee->weekly_hours ?? 40) }}" step="0.5" min="0" max="60" required
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
    </div>

    {{-- Vacation Days --}}
    <div>
        <label for="vacation_days_per_year" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.vacation') }} / Year *</label>
        <input type="number" name="vacation_days_per_year" id="vacation_days_per_year" value="{{ old('vacation_days_per_year', $employee->vacation_days_per_year ?? 30) }}" min="0" max="60" required
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
    </div>

    {{-- Employment Start --}}
    <div>
        <label for="employment_start" class="block text-sm font-medium text-gray-700 mb-1">Employment Start</label>
        <input type="date" name="employment_start" id="employment_start" value="{{ old('employment_start', isset($employee) && $employee->employment_start ? $employee->employment_start->format('Y-m-d') : '') }}"
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
    </div>

    {{-- Employment End --}}
    <div>
        <label for="employment_end" class="block text-sm font-medium text-gray-700 mb-1">Employment End</label>
        <input type="date" name="employment_end" id="employment_end" value="{{ old('employment_end', isset($employee) && $employee->employment_end ? $employee->employment_end->format('Y-m-d') : '') }}"
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
    </div>

    {{-- Active (edit only) --}}
    @if(isset($employee))
    <div class="flex items-center">
        <label class="inline-flex items-center gap-x-2">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm font-medium text-gray-700">{{ __('app.active') }}</span>
        </label>
    </div>
    @endif
</div>
