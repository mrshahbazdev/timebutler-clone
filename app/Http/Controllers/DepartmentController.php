<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $departments = Department::where('organization_id', $user->organization_id)
            ->withCount('employees')
            ->with('manager')
            ->orderBy('name')
            ->get();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $user = auth()->user();
        $managers = User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('departments.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'color' => 'required|string|max:7',
        ]);

        Department::create([
            'organization_id' => $request->user()->organization_id,
            'name' => $validated['name'],
            'manager_id' => $validated['manager_id'],
            'color' => $validated['color'],
        ]);

        return redirect()->route('departments.index')
            ->with('success', __('app.success'));
    }

    public function edit(Department $department)
    {
        $user = auth()->user();
        $managers = User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('departments.edit', compact('department', 'managers'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'color' => 'required|string|max:7',
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')
            ->with('success', __('app.success'));
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', __('app.success'));
    }
}
