<?php

use App\Models\AbsenceType;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
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

        Organization::each(function (Organization $org) use ($types) {
            if ($org->absenceTypes()->count() === 0) {
                foreach ($types as $type) {
                    AbsenceType::create(array_merge($type, ['organization_id' => $org->id]));
                }
            }
        });
    }

    public function down(): void
    {
        // No rollback needed
    }
};
