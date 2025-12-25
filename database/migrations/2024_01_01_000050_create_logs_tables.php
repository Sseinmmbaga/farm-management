<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Base Logs Table (farmOS-style Activity Logs)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // seeding, input, observation, harvest, activity, training, inspection
            $table->string('name');
            $table->text('description')->nullable();
            $table->datetime('log_date');
            $table->enum('status', ['pending', 'done', 'cancelled'])->default('done');

            // References
            $table->foreignId('farmer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();

            // Location
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('geometry')->nullable();

            // Additional data
            $table->json('data')->nullable();
            $table->text('notes')->nullable();
            $table->json('images')->nullable(); // array of image paths

            // Flags
            $table->boolean('is_flagged')->default(false);
            $table->string('flag_reason')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('log_date');
            $table->index('status');
            $table->index('farmer_id');
            $table->index('farm_id');
            $table->index('asset_id');
            $table->index('season_id');
        });

        // Seeding Logs (Kumbukumbu za Kupanda)
        Schema::create('seeding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_log_id')->constrained()->cascadeOnDelete();
            $table->string('crop_type');
            $table->string('variety')->nullable();
            $table->string('seed_source')->nullable();
            $table->string('seed_lot_number')->nullable();
            $table->decimal('seed_quantity', 10, 2)->nullable();
            $table->string('seed_unit')->default('kg');
            $table->decimal('area_seeded', 10, 2)->nullable();
            $table->string('area_unit')->default('hectares');
            $table->string('seeding_method')->nullable(); // direct, transplant, broadcast
            $table->decimal('row_spacing', 6, 2)->nullable(); // cm
            $table->decimal('plant_spacing', 6, 2)->nullable(); // cm
            $table->decimal('seeding_depth', 6, 2)->nullable(); // cm
            $table->string('soil_preparation')->nullable();
            $table->timestamps();

            $table->index('activity_log_id');
            $table->index('crop_type');
        });

        // Input Logs (Kumbukumbu za Pembejeo)
        Schema::create('input_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_log_id')->constrained()->cascadeOnDelete();
            $table->string('input_type'); // fertilizer, pesticide, herbicide, organic_input
            $table->string('product_name');
            $table->string('brand')->nullable();
            $table->string('active_ingredient')->nullable();
            $table->decimal('quantity_applied', 10, 2);
            $table->string('quantity_unit');
            $table->decimal('application_rate', 10, 4)->nullable();
            $table->string('rate_unit')->nullable(); // kg/ha, L/ha
            $table->decimal('area_treated', 10, 2)->nullable();
            $table->string('application_method')->nullable(); // spray, broadcast, drip
            $table->string('target_pest')->nullable();
            $table->string('weather_conditions')->nullable();
            $table->integer('pre_harvest_interval')->nullable(); // days
            $table->boolean('is_organic_approved')->default(false);
            $table->timestamps();

            $table->index('activity_log_id');
            $table->index('input_type');
        });

        // Observation Logs (Uchunguzi)
        Schema::create('observation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_log_id')->constrained()->cascadeOnDelete();
            $table->string('observation_type'); // crop_health, pest, disease, weather, growth
            $table->string('category')->nullable();
            $table->string('severity')->nullable(); // low, medium, high, critical
            $table->decimal('affected_area', 10, 2)->nullable();
            $table->string('area_unit')->default('hectares');
            $table->decimal('affected_percentage', 5, 2)->nullable();
            $table->string('growth_stage')->nullable();
            $table->string('plant_health')->nullable(); // excellent, good, fair, poor
            $table->text('symptoms')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('requires_action')->default(false);
            $table->string('action_taken')->nullable();
            $table->timestamps();

            $table->index('activity_log_id');
            $table->index('observation_type');
            $table->index('severity');
        });

        // Harvest Logs (Kumbukumbu za Mavuno)
        Schema::create('harvest_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_log_id')->constrained()->cascadeOnDelete();
            $table->string('crop_type');
            $table->string('variety')->nullable();
            $table->decimal('area_harvested', 10, 2);
            $table->string('area_unit')->default('hectares');
            $table->decimal('quantity_harvested', 12, 2);
            $table->string('quantity_unit')->default('kg');
            $table->decimal('yield_per_hectare', 10, 2)->nullable();
            $table->string('quality_grade')->nullable(); // A, B, C or descriptive
            $table->decimal('moisture_content', 5, 2)->nullable(); // percentage
            $table->string('harvest_method')->nullable(); // manual, mechanical
            $table->integer('labor_hours')->nullable();
            $table->integer('workers_count')->nullable();
            $table->string('storage_location')->nullable();
            $table->string('buyer')->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('price_unit')->nullable();
            $table->timestamps();

            $table->index('activity_log_id');
            $table->index('crop_type');
            $table->index('quality_grade');
        });

        // Training Logs (Kumbukumbu za Mafunzo)
        Schema::create('training_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_log_id')->constrained()->cascadeOnDelete();
            $table->string('training_type'); // workshop, field_day, demonstration, lecture
            $table->string('topic');
            $table->text('learning_objectives')->nullable();
            $table->string('trainer_name')->nullable();
            $table->string('trainer_organization')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->integer('attendees_count')->nullable();
            $table->integer('male_attendees')->nullable();
            $table->integer('female_attendees')->nullable();
            $table->string('venue')->nullable();
            $table->json('materials_used')->nullable();
            $table->text('feedback')->nullable();
            $table->boolean('certificates_issued')->default(false);
            $table->timestamps();

            $table->index('activity_log_id');
            $table->index('training_type');
        });

        // Inspection Logs (Kumbukumbu za Ukaguzi) - ICS
        Schema::create('inspection_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_log_id')->constrained()->cascadeOnDelete();
            $table->string('inspection_type'); // internal, external, routine, surprise
            $table->string('inspector_name');
            $table->string('inspector_organization')->nullable();
            $table->string('certification_body')->nullable();
            $table->json('checklist_items')->nullable();
            $table->integer('total_items')->default(0);
            $table->integer('compliant_items')->default(0);
            $table->integer('non_compliant_items')->default(0);
            $table->decimal('compliance_score', 5, 2)->nullable(); // percentage
            $table->enum('result', ['passed', 'failed', 'conditional', 'pending'])->default('pending');
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->boolean('corrective_action_required')->default(false);
            $table->timestamps();

            $table->index('activity_log_id');
            $table->index('inspection_type');
            $table->index('result');
        });

        // Log-Asset Relationship (many-to-many)
        Schema::create('activity_log_asset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['activity_log_id', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log_asset');
        Schema::dropIfExists('inspection_logs');
        Schema::dropIfExists('training_logs');
        Schema::dropIfExists('harvest_logs');
        Schema::dropIfExists('observation_logs');
        Schema::dropIfExists('input_logs');
        Schema::dropIfExists('seeding_logs');
        Schema::dropIfExists('activity_logs');
    }
};
