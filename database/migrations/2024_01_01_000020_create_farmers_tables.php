<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Farmer Groups (Vikundi)
        Schema::create('farmer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->string('leader_name')->nullable();
            $table->string('leader_phone')->nullable();
            $table->date('established_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Farmers (Wakulima)
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique(); // Namba ya Usajili
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id')->nullable(); // NIDA
            $table->string('phone')->nullable();
            $table->string('phone_alt')->nullable();
            $table->string('email')->nullable();

            // Location
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Group & Assignment
            $table->foreignId('farmer_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('extension_officer_id')->nullable()->constrained('users')->nullOnDelete();

            // Status
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('pending');
            $table->date('registration_date')->nullable();
            $table->date('certification_date')->nullable();
            $table->string('certification_status')->nullable(); // organic, conventional, in-conversion

            // Additional Info
            $table->integer('household_size')->nullable();
            $table->string('education_level')->nullable();
            $table->decimal('total_land_size', 10, 2)->nullable(); // in hectares
            $table->text('notes')->nullable();
            $table->string('photo')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['region_id', 'district_id', 'village_id']);
            $table->index('extension_officer_id');
            $table->index('farmer_group_id');
            $table->index('status');
        });

        // Farmer Documents (Nyaraka)
        Schema::create('farmer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // national_id, certificate, contract, photo
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['farmer_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_documents');
        Schema::dropIfExists('farmers');
        Schema::dropIfExists('farmer_groups');
    }
};
