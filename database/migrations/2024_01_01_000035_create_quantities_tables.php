<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Units of Measurement (Vipimo)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Kilogram, Hectare, Liter
            $table->string('name_sw')->nullable(); // Swahili name
            $table->string('symbol'); // kg, ha, L
            $table->string('category'); // weight, area, volume, count, length
            $table->decimal('conversion_factor', 15, 8)->default(1); // for converting to base unit
            $table->string('base_unit')->nullable(); // reference to base unit symbol
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['symbol', 'category']);
            $table->index('category');
        });

        // Quantities (Kiasi) - Polymorphic for any model
        Schema::create('quantities', function (Blueprint $table) {
            $table->id();
            $table->morphs('quantifiable'); // links to any model (farm, log, asset, etc.)
            $table->decimal('value', 15, 4);
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable(); // area, yield, weight, etc.
            $table->string('measure_type')->nullable(); // estimated, measured, calculated
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('label');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quantities');
        Schema::dropIfExists('units');
    }
};
