<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organization;

        return view('settings.index', compact('organization'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'timezone' => 'required|string|timezone',
            'default_vacation_days' => 'required|integer|min:0|max:365',
            'work_hours_per_day' => 'required|numeric|min:1|max:24',
        ]);

        $organization = $request->user()->organization;
        $organization->update($validated);

        return back()->with('success', app()->getLocale() === 'de' ? 'Einstellungen gespeichert.' : 'Settings saved.');
    }
}
