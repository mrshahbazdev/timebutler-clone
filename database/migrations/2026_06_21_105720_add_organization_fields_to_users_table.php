<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->after('department_id')->constrained('users')->nullOnDelete();
            $table->string('locale')->default('de')->after('email');
            $table->string('avatar_path')->nullable()->after('locale');
            $table->string('employee_number')->nullable()->after('avatar_path');
            $table->string('position')->nullable()->after('employee_number');
            $table->string('phone')->nullable()->after('position');
            $table->decimal('weekly_hours', 4, 1)->default(40.0)->after('phone');
            $table->integer('vacation_days_per_year')->default(30)->after('weekly_hours');
            $table->date('employment_start')->nullable()->after('vacation_days_per_year');
            $table->date('employment_end')->nullable()->after('employment_start');
            $table->boolean('is_active')->default(true)->after('employment_end');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['manager_id']);
            $table->dropColumn([
                'organization_id', 'department_id', 'manager_id',
                'locale', 'avatar_path', 'employee_number', 'position',
                'phone', 'weekly_hours', 'vacation_days_per_year',
                'employment_start', 'employment_end', 'is_active'
            ]);
        });
    }
};
