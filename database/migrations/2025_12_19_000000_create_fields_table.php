<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fields/Plots within a farm
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable(); // Field code (e.g., FIELD-001)
            $table->string('name'); // Field name (e.g., North Field, Plot A)
            
            // Location within farm
            $table->string('location_description')->nullable(); // e.g., "Northwest corner"
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('boundary_coordinates')->nullable(); // GeoJSON polygon for field boundary
            
            // Field Details
            $table->decimal('total_area', 10, 2); // hectares or acres
            $table->string('measurement_unit')->default('acres'); // acres, hectares
            $table->string('soil_type')->nullable();
            $table->string('soil_ph')->nullable(); // pH level
            $table->string('soil_texture')->nullable(); // sandy, loamy, clay
            $table->decimal('slope_percentage', 5, 2)->nullable(); // slope percentage
            $table->string('drainage')->nullable(); // good, moderate, poor
            
            // Current Crop Information
            $table->string('current_crop_type')->nullable(); // cotton, sesame, maize, etc.
            $table->string('crop_variety')->nullable(); // specific variety
            $table->date('planting_date')->nullable();
            $table->date('expected_harvest_date')->nullable();
            $table->date('actual_harvest_date')->nullable();
            $table->decimal('expected_yield', 10, 2)->nullable(); // kg or tons
            $table->decimal('actual_yield', 10, 2)->nullable();
            $table->string('yield_unit')->default('kg'); // kg, tons
            
            // Field Status
            $table->enum('status', [
                'active',        // Currently cultivated
                'fallow',        // Resting/not cultivated
                'prepared',      // Land prepared for planting
                'planted',       // Crop planted
                'growing',       // Crop growing
                'harvested',     // Crop harvested
                'abandoned',     // Not in use
                'converted',     // Converted to other use
            ])->default('active');
            
            // Irrigation
            $table->string('irrigation_type')->nullable(); // drip, sprinkler, flood, rain-fed
            $table->string('irrigation_source')->nullable(); // well, river, dam, rain
            $table->integer('irrigation_frequency_days')->nullable(); // days between irrigation
            
            // Certification
            $table->boolean('is_organic')->default(false);
            $table->date('organic_certified_since')->nullable();
            $table->string('certification_body')->nullable();
            $table->string('certification_number')->nullable();
            
            // Rotation Information
            $table->string('previous_crop')->nullable();
            $table->string('next_planned_crop')->nullable();
            $table->date('rotation_date')->nullable();
            
            // Notes and Metadata
            $table->text('notes')->nullable();
            $table->date('establishment_date')->nullable(); // When field was established
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('farm_id');
            $table->index('status');
            $table->index('current_crop_type');
            $table->unique(['farm_id', 'code']);
        });
        
        // Field History (for tracking changes and crop rotations)
        Schema::create('field_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();
            $table->year('year');
            
            // Crop Information
            $table->string('crop_type'); // cotton, sesame, etc.
            $table->string('crop_variety')->nullable();
            $table->decimal('area_planted', 10, 2); // hectares or acres
            $table->date('planting_date')->nullable();
            $table->date('harvest_date')->nullable();
            $table->decimal('yield_amount', 10, 2)->nullable(); // kg or tons
            $table->decimal('yield_per_unit_area', 10, 2)->nullable(); // yield per hectare/acre
            
            // Inputs and Practices
            $table->text('fertilizers_used')->nullable();
            $table->text('pesticides_used')->nullable();
            $table->text('irrigation_details')->nullable();
            $table->text('farming_practices')->nullable(); // organic, conventional, etc.
            
            // Quality and Results
            $table->string('quality_grade')->nullable();
            $table->decimal('production_cost', 10, 2)->nullable();
            $table->decimal('revenue', 10, 2)->nullable();
            $table->decimal('profit', 10, 2)->nullable();
            
            // Challenges and Notes
            $table->text('challenges')->nullable();
            $table->text('notes')->nullable();
            
            // Record keeping
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index(['field_id', 'year']);
            $table->index(['field_id', 'season_id']);
            $table->index('crop_type');
        });
        
        // Field Boundaries (detailed GPS mapping for individual fields)
        Schema::create('field_boundaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->integer('point_order');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude', 8, 2)->nullable();
            $table->decimal('accuracy', 6, 2)->nullable(); // GPS accuracy in meters
            $table->timestamp('captured_at')->nullable();
            $table->foreignId('captured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('field_id');
            $table->unique(['field_id', 'point_order']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('field_boundaries');
        Schema::dropIfExists('field_histories');
        Schema::dropIfExists('fields');
    }
};