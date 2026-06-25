<?php

namespace App\Http\Controllers;

use App\Models\AbsenceType;
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

        $this->seedAbsenceTypes($organization);

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

        \Illuminate\Support\Facades\Artisan::call('permissions:sync');

        $user->assignRole('admin');

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    private function seedAbsenceTypes(Organization $organization): void
    {
        $types = [
            ['name_en' => 'Vacation', 'name_de' => 'Urlaub', 'color' => '#3b82f6', 'deducts_vacation' => true, 'sort_order' => 1],
            ['name_en' => 'Sick Leave (with note)', 'name_de' => 'Krankheit (mit Attest)', 'color' => '#ef4444', 'requires_certificate' => true, 'requires_approval' => false, 'sort_order' => 2],
            ['name_en' => 'Sick Leave (without note)', 'name_de' => 'Krankheit (ohne Attest)', 'color' => '#f87171', 'requires_certificate' => false, 'requires_approval' => false, 'sort_order' => 3],
            ['name_en' => 'Sick Child Care', 'name_de' => 'Kind krank', 'color' => '#fb923c', 'requires_certificate' => false, 'requires_approval' => false, 'sort_order' => 4],
            ['name_en' => 'Home Office', 'name_de' => 'Homeoffice', 'color' => '#10b981', 'requires_approval' => false, 'deducts_vacation' => false, 'sort_order' => 5],
            ['name_en' => 'Business Trip', 'name_de' => 'Dienstreise', 'color' => '#8b5cf6', 'deducts_vacation' => false, 'sort_order' => 6],
            ['name_en' => 'Parental Leave', 'name_de' => 'Elternzeit', 'color' => '#f97316', 'deducts_vacation' => false, 'sort_order' => 7],
            ['name_en' => 'Special Leave', 'name_de' => 'Sonderurlaub', 'color' => '#06b6d4', 'deducts_vacation' => false, 'sort_order' => 8],
            ['name_en' => 'Continuing Education', 'name_de' => 'Weiterbildung', 'color' => '#84cc16', 'deducts_vacation' => false, 'sort_order' => 9],
        ];

        foreach ($types as $type) {
            AbsenceType::create(array_merge($type, ['organization_id' => $organization->id]));
        }
    }
}
