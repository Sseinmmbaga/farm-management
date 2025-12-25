<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Seasons (Misimu)
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "2023/2024"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Farms/Shamba
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique(); // Namba ya Shamba
            $table->string('name')->nullable();

            // Location
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('boundary_coordinates')->nullable(); // GeoJSON polygon

            // Farm Details
            $table->decimal('total_area', 10, 2)->nullable(); // hectares
            $table->decimal('cultivated_area', 10, 2)->nullable();
            $table->string('soil_type')->nullable();
            $table->string('water_source')->nullable();
            $table->string('terrain')->nullable(); // flat, hilly, etc.

            // Certification
            $table->string('certification_status')->default('conventional'); // organic, in-conversion, conventional
            $table->date('organic_since')->nullable();
            $table->integer('conversion_year')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive', 'abandoned'])->default('active');
            $table->date('registration_date')->nullable();
            $table->text('notes')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('farmer_id');
            $table->index('status');
            $table->index('certification_status');
        });

        // Farm History (Historia ya Shamba)
        Schema::create('farm_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();
            $table->year('year');
            $table->string('crop_type'); // cotton, sesame, etc.
            $table->decimal('area_planted', 10, 2)->nullable(); // hectares
            $table->decimal('yield_amount', 10, 2)->nullable(); // kg
            $table->decimal('yield_per_hectare', 10, 2)->nullable();
            $table->string('quality_grade')->nullable();
            $table->text('farming_practices')->nullable();
            $table->text('inputs_used')->nullable();
            $table->text('challenges')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['farm_id', 'year']);
            $table->index(['farm_id', 'season_id']);
        });

        // Farm Boundaries (for detailed GPS mapping)
        Schema::create('farm_boundaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->integer('point_order');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude', 8, 2)->nullable();
            $table->decimal('accuracy', 6, 2)->nullable(); // GPS accuracy in meters
            $table->timestamp('captured_at')->nullable();
            $table->foreignId('captured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('farm_id');
            $table->unique(['farm_id', 'point_order']);
        });

        // Farm Seasons (Msimu wa Shamba - linking farms to seasons with specific data)
        Schema::create('farm_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->string('primary_crop'); // main crop for this season
            $table->string('secondary_crop')->nullable();
            $table->decimal('planned_area', 10, 2)->nullable();
            $table->decimal('actual_area', 10, 2)->nullable();
            $table->date('planting_date')->nullable();
            $table->date('expected_harvest_date')->nullable();
            $table->date('actual_harvest_date')->nullable();
            $table->decimal('expected_yield', 10, 2)->nullable();
            $table->decimal('actual_yield', 10, 2)->nullable();
            $table->enum('status', ['planned', 'planted', 'growing', 'harvested', 'failed'])->default('planned');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['farm_id', 'season_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_seasons');
        Schema::dropIfExists('farm_boundaries');
        Schema::dropIfExists('farm_histories');
        Schema::dropIfExists('farms');
        Schema::dropIfExists('seasons');
    }
};
