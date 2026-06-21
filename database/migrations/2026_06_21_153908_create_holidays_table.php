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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->enum('type', ['public_holiday', 'school_break', 'weekend', 'custom']);
            $table->string('federal_state')->nullable();
            $table->integer('year');
            $table->boolean('is_half_day')->default(false);
            $table->timestamps();

            $table->unique(['organization_id', 'date', 'type', 'federal_state'], 'holidays_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
