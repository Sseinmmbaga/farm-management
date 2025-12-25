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
        // Core field_entries table
        Schema::create('field_entries', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('entry_code')->unique();
            $table->string('title');
            
            // Relationships
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farmer_id')->nullable()->constrained()->nullOnDelete();
            
            // Form Type
            $table->enum('form_type', [
                'training',
                'production',
                'inspection',
                'input_application',
                'harvest',
                'soil_test',
                'irrigation',
                'pest_disease',
                'weather',
                'equipment',
                'labor',
                'certification',
                'other'
            ])->default('production');
            
            $table->string('form_subtype')->nullable();
            
            // Entry Details
            $table->date('entry_date');
            $table->time('entry_time')->nullable();
            $table->year('season_year');
            $table->string('season_period')->nullable();
            
            // Status
            $table->enum('status', [
                'draft',
                'submitted',
                'reviewed',
                'approved',
                'rejected',
                'archived'
            ])->default('draft');
            
            // Location
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_notes')->nullable();
            
            // Weather
            $table->string('weather_condition')->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
            $table->decimal('rainfall', 6, 2)->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('summary')->nullable();
            
            // Audit
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Field Entry Data - Flexible JSON storage
        Schema::create('field_entry_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_entry_id')->constrained()->cascadeOnDelete();
            $table->string('section_name');
            $table->integer('section_order')->default(0);
            $table->json('data');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_entry_data');
        Schema::dropIfExists('field_entries');
    }
};
