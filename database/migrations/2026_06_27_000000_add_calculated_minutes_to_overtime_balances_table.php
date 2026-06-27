<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('overtime_balances', function (Blueprint $table) {
            $table->integer('calculated_minutes')->default(0)->after('month');
            $table->integer('adjustment_minutes')->default(0)->after('calculated_minutes');
        });

        // Set the adjustment_minutes to the current balance_minutes, 
        // because historically all balances were manual adjustments.
        DB::statement('UPDATE overtime_balances SET adjustment_minutes = balance_minutes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtime_balances', function (Blueprint $table) {
            $table->dropColumn(['calculated_minutes', 'adjustment_minutes']);
        });
    }
};
