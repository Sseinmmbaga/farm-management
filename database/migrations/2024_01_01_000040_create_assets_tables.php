<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Base Assets Table (farmOS-style)
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // land, crop, equipment, material, group
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, inactive, archived

            // Owner/Location
            $table->foreignId('farmer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('geometry')->nullable(); // GeoJSON for complex shapes

            // Hierarchy (for parent-child relationships)
            $table->foreignId('parent_id')->nullable()->constrained('assets')->nullOnDelete();

            // Dates
            $table->date('acquired_date')->nullable();
            $table->date('disposed_date')->nullable();

            // Additional data stored as JSON
            $table->json('data')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('status');
            $table->index('farmer_id');
            $table->index('farm_id');
            $table->index('parent_id');
        });

        // Land Assets (Mashamba - additional details)
        Schema::create('land_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('land_type'); // field, paddock, property, greenhouse
            $table->decimal('total_area', 10, 2)->nullable();
            $table->string('area_unit')->default('hectares');
            $table->string('soil_type')->nullable();
            $table->string('irrigation_type')->nullable();
            $table->string('land_use')->nullable(); // cultivation, fallow, pasture
            $table->boolean('is_organic')->default(false);
            $table->year('organic_since')->nullable();
            $table->json('boundary_points')->nullable();
            $table->timestamps();

            $table->index('asset_id');
            $table->index('land_type');
        });

        // Crop Assets (Mazao)
        Schema::create('crop_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();
            $table->string('crop_type'); // cotton, sesame, sunflower
            $table->string('variety')->nullable();
            $table->date('planting_date')->nullable();
            $table->date('expected_harvest_date')->nullable();
            $table->date('actual_harvest_date')->nullable();
            $table->decimal('planted_area', 10, 2)->nullable();
            $table->string('growth_stage')->nullable(); // seedling, vegetative, flowering, mature
            $table->decimal('expected_yield', 10, 2)->nullable();
            $table->decimal('actual_yield', 10, 2)->nullable();
            $table->string('yield_unit')->default('kg');
            $table->timestamps();

            $table->index('asset_id');
            $table->index('crop_type');
            $table->index('season_id');
        });

        // Equipment Assets (Vifaa)
        Schema::create('equipment_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('equipment_type'); // tractor, sprayer, tools
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->year('year_manufactured')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->string('condition')->default('good'); // excellent, good, fair, poor
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();

            $table->index('asset_id');
            $table->index('equipment_type');
        });

        // Material Assets (Pembejeo - seeds, fertilizers, etc.)
        Schema::create('material_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('material_type'); // seed, fertilizer, pesticide, organic_input
            $table->string('material_name');
            $table->string('brand')->nullable();
            $table->string('batch_number')->nullable();
            $table->decimal('quantity', 12, 2)->nullable();
            $table->string('quantity_unit')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_organic_approved')->default(false);
            $table->string('certification')->nullable();
            $table->timestamps();

            $table->index('asset_id');
            $table->index('material_type');
        });

        // Group Assets (Vikundi - for grouping farmers or assets)
        Schema::create('group_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('group_type'); // farmer_group, cooperative, zone
            $table->integer('member_count')->default(0);
            $table->string('leader_name')->nullable();
            $table->string('leader_contact')->nullable();
            $table->date('formation_date')->nullable();
            $table->timestamps();

            $table->index('asset_id');
            $table->index('group_type');
        });

        // Asset Group Members (link table)
        Schema::create('asset_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_asset_id')->constrained('group_assets')->cascadeOnDelete();
            $table->morphs('member'); // can link to farmers, other assets, etc.
            $table->string('role')->nullable(); // leader, member, treasurer
            $table->date('joined_date')->nullable();
            $table->date('left_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('group_asset_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_group_members');
        Schema::dropIfExists('group_assets');
        Schema::dropIfExists('material_assets');
        Schema::dropIfExists('equipment_assets');
        Schema::dropIfExists('crop_assets');
        Schema::dropIfExists('land_assets');
        Schema::dropIfExists('assets');
    }
};
