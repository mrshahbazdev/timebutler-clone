<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absence_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name_en');
            $table->string('name_de');
            $table->string('color')->default('#ef4444');
            $table->string('icon')->default('calendar-x');
            $table->boolean('requires_approval')->default(true);
            $table->boolean('requires_certificate')->default(false);
            $table->boolean('is_paid')->default(true);
            $table->boolean('deducts_vacation')->default(false);
            $table->integer('max_days_per_year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_types');
    }
};
