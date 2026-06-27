<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('time_entries_user_id_date_unique');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unique(['user_id', 'date'], 'time_entries_user_id_date_unique');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
