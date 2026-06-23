<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\VacationBalance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = User::where('organization_id', $user->organization_id)
            ->with(['department', 'manager']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($department = $request->get('department')) {
            $query->where('department_id', $department);
        }

        if ($status = $request->get('status')) {
            $query->where('is_active', $status === 'active');
        }

        $employees = $query->orderBy('name')->paginate(15)->withQueryString();

        $departments = Department::where('organization_id', $user->organization_id)->get();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $user = auth()->user();
        $departments = Department::where('organization_id', $user->organization_id)->get();
        $managers = User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $roles = Role::all();

        return view('employees.create', compact('departments', 'managers', 'roles'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'employee_number' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'weekly_hours' => 'required|numeric|min:0|max:60',
            'vacation_days_per_year' => 'required|integer|min:0|max:60',
            'employment_start' => 'nullable|date',
            'employment_end' => 'nullable|date|after:employment_start',
            'role' => 'required|exists:roles,name',
            'locale' => 'required|in:en,de',
        ]);

        $employee = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'organization_id' => $user->organization_id,
            'department_id' => $validated['department_id'],
            'manager_id' => $validated['manager_id'],
            'employee_number' => $validated['employee_number'],
            'position' => $validated['position'],
            'phone' => $validated['phone'],
            'weekly_hours' => $validated['weekly_hours'],
            'vacation_days_per_year' => $validated['vacation_days_per_year'],
            'employment_start' => $validated['employment_start'],
            'employment_end' => $validated['employment_end'],
            'locale' => $validated['locale'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $employee->assignRole($validated['role']);

        VacationBalance::create([
            'user_id' => $employee->id,
            'organization_id' => $user->organization_id,
            'year' => now()->year,
            'total_days' => $validated['vacation_days_per_year'],
            'used_days' => 0,
            'remaining_days' => $validated['vacation_days_per_year'],
        ]);

        return redirect()->route('employees.index')
            ->with('success', __('app.success'));
    }

    public function show(User $employee)
    {
        $employee->load(['department', 'manager', 'absenceRequests.absenceType', 'vacationBalances']);

        $recentAbsences = $employee->absenceRequests()
            ->with('absenceType')
            ->latest()
            ->take(10)
            ->get();

        $recentTimeEntries = $employee->timeEntries()
            ->orderByDesc('date')
            ->take(10)
            ->get();

        $vacationBalance = $employee->vacationBalances()
            ->where('year', now()->year)
            ->first();

        return view('employees.show', compact('employee', 'recentAbsences', 'recentTimeEntries', 'vacationBalance'));
    }

    public function edit(User $employee)
    {
        $user = auth()->user();
        $departments = Department::where('organization_id', $user->organization_id)->get();
        $managers = User::where('organization_id', $user->organization_id)
            ->where('id', '!=', $employee->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $roles = Role::all();

        return view('employees.edit', compact('employee', 'departments', 'managers', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($employee->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'employee_number' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'weekly_hours' => 'required|numeric|min:0|max:60',
            'vacation_days_per_year' => 'required|integer|min:0|max:60',
            'employment_start' => 'nullable|date',
            'employment_end' => 'nullable|date|after:employment_start',
            'role' => 'required|exists:roles,name',
            'locale' => 'required|in:en,de',
            'is_active' => 'boolean',
        ]);

        $updateData = collect($validated)->except(['password', 'role', 'is_active'])->toArray();
        $updateData['is_active'] = $request->boolean('is_active', true);

        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $employee->update($updateData);
        $employee->syncRoles([$validated['role']]);

        // Sync VacationBalance total_days when vacation_days_per_year changes
        $vacationBalance = VacationBalance::where('user_id', $employee->id)
            ->where('year', now()->year)
            ->first();

        if ($vacationBalance) {
            $diff = $validated['vacation_days_per_year'] - $vacationBalance->total_days;
            $vacationBalance->update([
                'total_days' => $validated['vacation_days_per_year'],
                'remaining_days' => max(0, $vacationBalance->remaining_days + $diff),
            ]);
        } else {
            VacationBalance::create([
                'user_id' => $employee->id,
                'organization_id' => $employee->organization_id,
                'year' => now()->year,
                'total_days' => $validated['vacation_days_per_year'],
                'used_days' => 0,
                'remaining_days' => $validated['vacation_days_per_year'],
            ]);
        }

        return redirect()->route('employees.index')
            ->with('success', __('app.success'));
    }

    public function destroy(User $employee)
    {
        $employee->update(['is_active' => false]);

        return redirect()->route('employees.index')
            ->with('success', __('app.success'));
    }
}
