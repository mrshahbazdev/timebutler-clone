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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('federal_state')->default('NW')->after('country_code');
        });

        Schema::table('absence_requests', function (Blueprint $table) {
            $table->enum('request_type', ['request', 'blocked'])->default('request')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('federal_state');
        });

        Schema::table('absence_requests', function (Blueprint $table) {
            $table->dropColumn('request_type');
        });
    }
};
