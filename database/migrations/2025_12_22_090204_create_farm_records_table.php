<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_records', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();

            // Record type: 'new' for new farm records (form2), 'existing' for existing farm records (form3)
            $table->enum('record_type', ['new', 'existing']);

            // Certification status (C0, C1, C2, O)
            $table->enum('certification_status', ['C0', 'C1', 'C2', 'O'])->nullable();

            // Livestock counts
            $table->unsignedInteger('cattle_count')->default(0);
            $table->unsignedInteger('goats_sheep_count')->default(0);
            $table->unsignedInteger('oxen_count')->default(0);

            // Equipment ownership
            $table->boolean('has_input_book')->default(false);
            $table->boolean('has_pump')->default(false);

            // Chemical/Residue status
            $table->boolean('has_chemical_seed_residue')->default(false);
            $table->boolean('has_chemical_residue')->default(false);

            // Farm area details
            $table->decimal('area_size', 10, 2)->nullable(); // in hectares

            // Fields for existing farms (form3)
            $table->decimal('land_bought', 10, 2)->nullable(); // land bought in hectares
            $table->decimal('land_sold', 10, 2)->nullable(); // land sold in hectares
            $table->decimal('land_borrowed', 10, 2)->nullable(); // land borrowed in hectares
            $table->decimal('land_lent', 10, 2)->nullable(); // land lent in hectares

            // Registration and tracking
            $table->year('registration_year')->nullable();
            $table->string('impact_type')->nullable(); // type of impact/change

            // Additional notes
            $table->text('notes')->nullable();

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index(['farm_id', 'season_id']);
            $table->index(['farmer_id', 'season_id']);
            $table->index('record_type');
            $table->index('certification_status');

            // Ensure one record per farm per season per type
            $table->unique(['farm_id', 'season_id', 'record_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_records');
    }
};
