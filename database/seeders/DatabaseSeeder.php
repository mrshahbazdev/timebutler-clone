<?php

namespace Database\Seeders;

use App\Models\AbsenceRequest;
use App\Models\AbsenceType;
use App\Models\Department;
use App\Models\Organization;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\VacationBalance;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $employee = Role::create(['name' => 'employee']);

        // Create organization
        $org = Organization::create([
            'name' => 'Demo GmbH',
            'slug' => 'demo-gmbh',
            'country_code' => 'DE',
            'timezone' => 'Europe/Berlin',
            'default_locale' => 'de',
            'default_vacation_days' => 30,
        ]);

        // Create departments
        $engineering = Department::create([
            'organization_id' => $org->id,
            'name' => 'Engineering',
            'color' => '#3b82f6',
        ]);

        $hr = Department::create([
            'organization_id' => $org->id,
            'name' => 'Human Resources',
            'color' => '#10b981',
        ]);

        $marketing = Department::create([
            'organization_id' => $org->id,
            'name' => 'Marketing',
            'color' => '#f59e0b',
        ]);

        // Create absence types
        $absenceTypes = [
            ['name_en' => 'Vacation', 'name_de' => 'Urlaub', 'color' => '#3b82f6', 'deducts_vacation' => true, 'sort_order' => 1],
            ['name_en' => 'Sick Leave', 'name_de' => 'Krankheit', 'color' => '#ef4444', 'requires_certificate' => true, 'requires_approval' => false, 'sort_order' => 2],
            ['name_en' => 'Home Office', 'name_de' => 'Homeoffice', 'color' => '#10b981', 'requires_approval' => false, 'deducts_vacation' => false, 'sort_order' => 3],
            ['name_en' => 'Business Trip', 'name_de' => 'Dienstreise', 'color' => '#8b5cf6', 'deducts_vacation' => false, 'sort_order' => 4],
            ['name_en' => 'Parental Leave', 'name_de' => 'Elternzeit', 'color' => '#f97316', 'deducts_vacation' => false, 'sort_order' => 5],
            ['name_en' => 'Special Leave', 'name_de' => 'Sonderurlaub', 'color' => '#06b6d4', 'deducts_vacation' => false, 'sort_order' => 6],
            ['name_en' => 'Training', 'name_de' => 'Weiterbildung', 'color' => '#84cc16', 'deducts_vacation' => false, 'sort_order' => 7],
        ];

        foreach ($absenceTypes as $type) {
            AbsenceType::create(array_merge($type, ['organization_id' => $org->id]));
        }

        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
            'password' => bcrypt('password'),
            'organization_id' => $org->id,
            'department_id' => $hr->id,
            'locale' => 'de',
            'position' => 'HR Director',
            'weekly_hours' => 40,
            'vacation_days_per_year' => 30,
            'employment_start' => '2020-01-01',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        // Create manager
        $managerUser = User::create([
            'name' => 'Max Mustermann',
            'email' => 'manager@demo.com',
            'password' => bcrypt('password'),
            'organization_id' => $org->id,
            'department_id' => $engineering->id,
            'locale' => 'de',
            'position' => 'Engineering Lead',
            'weekly_hours' => 40,
            'vacation_days_per_year' => 30,
            'employment_start' => '2019-06-01',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $managerUser->assignRole('manager');
        $engineering->update(['manager_id' => $managerUser->id]);

        // Create employees
        $emp1 = User::create([
            'name' => 'Anna Schmidt',
            'email' => 'anna@demo.com',
            'password' => bcrypt('password'),
            'organization_id' => $org->id,
            'department_id' => $engineering->id,
            'manager_id' => $managerUser->id,
            'locale' => 'de',
            'position' => 'Software Developer',
            'weekly_hours' => 40,
            'vacation_days_per_year' => 30,
            'employment_start' => '2021-03-15',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $emp1->assignRole('employee');

        $emp2 = User::create([
            'name' => 'Thomas Weber',
            'email' => 'thomas@demo.com',
            'password' => bcrypt('password'),
            'organization_id' => $org->id,
            'department_id' => $marketing->id,
            'manager_id' => $adminUser->id,
            'locale' => 'en',
            'position' => 'Marketing Manager',
            'weekly_hours' => 38.5,
            'vacation_days_per_year' => 28,
            'employment_start' => '2022-01-10',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $emp2->assignRole('employee');

        // Create vacation balances
        foreach ([$adminUser, $managerUser, $emp1, $emp2] as $user) {
            VacationBalance::create([
                'user_id' => $user->id,
                'organization_id' => $org->id,
                'year' => now()->year,
                'total_days' => $user->vacation_days_per_year,
                'used_days' => rand(3, 12),
                'remaining_days' => $user->vacation_days_per_year - rand(3, 12),
            ]);
        }

        // Create sample absence requests for admin
        $vacationType = AbsenceType::where('name_en', 'Vacation')->first();
        $sickType = AbsenceType::where('name_en', 'Sick Leave')->first();
        $homeOfficeType = AbsenceType::where('name_en', 'Home Office')->first();

        AbsenceRequest::create([
            'user_id' => $adminUser->id,
            'organization_id' => $org->id,
            'absence_type_id' => $vacationType->id,
            'start_date' => now()->addDays(14),
            'end_date' => now()->addDays(21),
            'total_days' => 6,
            'status' => 'approved',
            'approved_by' => $managerUser->id,
            'approved_at' => now()->subDays(2),
        ]);

        AbsenceRequest::create([
            'user_id' => $adminUser->id,
            'organization_id' => $org->id,
            'absence_type_id' => $sickType->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->subDays(3),
            'total_days' => 3,
            'status' => 'approved',
        ]);

        AbsenceRequest::create([
            'user_id' => $emp1->id,
            'organization_id' => $org->id,
            'absence_type_id' => $vacationType->id,
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(10),
            'total_days' => 4,
            'status' => 'pending',
        ]);

        // Create time entries for today (for admin)
        TimeEntry::create([
            'user_id' => $adminUser->id,
            'organization_id' => $org->id,
            'date' => today(),
            'start_time' => '08:30',
            'end_time' => null,
            'break_minutes' => 0,
            'total_minutes' => 0,
            'status' => 'draft',
        ]);

        // Create historical time entries
        for ($i = 1; $i <= 5; $i++) {
            $date = today()->subDays($i);
            if ($date->isWeekend()) continue;

            TimeEntry::create([
                'user_id' => $adminUser->id,
                'organization_id' => $org->id,
                'date' => $date,
                'start_time' => '08:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT),
                'end_time' => '17:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT),
                'break_minutes' => 30,
                'total_minutes' => rand(450, 510),
                'project' => ['Internal', 'Client A', 'Client B'][rand(0, 2)],
                'status' => 'submitted',
            ]);
        }
    }
}
