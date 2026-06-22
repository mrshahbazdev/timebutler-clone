<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->decimal('work_hours_per_day', 4, 1)->default(8.0)->after('default_vacation_days');
            $table->boolean('is_active')->default(true)->after('settings');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['work_hours_per_day', 'is_active']);
        });
    }
};
