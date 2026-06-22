<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Models\VacationBalance;
use App\Services\GermanHolidayService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CompanyRegistrationController extends Controller
{
    public function showForm()
    {
        $states = GermanHolidayService::FEDERAL_STATES;
        return view('auth.register-company', compact('states'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'federal_state' => 'required|string|in:' . implode(',', array_keys(GermanHolidayService::FEDERAL_STATES)),
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $organization = Organization::create([
            'name' => $validated['company_name'],
            'slug' => \Illuminate\Support\Str::slug($validated['company_name']),
            'federal_state' => $validated['federal_state'],
            'country_code' => 'DE',
            'timezone' => 'Europe/Berlin',
            'default_vacation_days' => 30,
            'work_hours_per_day' => 8.0,
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $organization->id,
            'is_active' => true,
            'locale' => 'de',
            'weekly_hours' => 40,
            'vacation_days_per_year' => 30,
            'employment_start' => now(),
        ]);

        $user->assignRole('admin');

        VacationBalance::create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'year' => now()->year,
            'total_days' => 30,
            'used_days' => 0,
            'remaining_days' => 30,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
