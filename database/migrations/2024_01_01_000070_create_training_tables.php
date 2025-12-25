<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Training Programs
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_sw')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->string('category')->nullable(); // organic_farming, pest_management, harvest, etc.
            $table->integer('duration_hours')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Training Sessions (Vipindi vya Mafunzo)
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('scheduled_date');
            $table->datetime('end_date')->nullable();
            $table->integer('duration_hours')->nullable();

            // Location
            $table->string('venue');
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Trainer
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('trainer_name')->nullable();
            $table->string('trainer_organization')->nullable();
            $table->string('trainer_contact')->nullable();

            // Capacity
            $table->integer('max_participants')->nullable();
            $table->integer('registered_count')->default(0);
            $table->integer('attended_count')->default(0);

            // Status
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->text('cancellation_reason')->nullable();

            // Materials
            $table->json('materials')->nullable();
            $table->json('equipment_needed')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('training_program_id');
            $table->index('scheduled_date');
            $table->index('status');
        });

        // Training Attendance (Mahudhurio)
        Schema::create('training_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->enum('registration_status', ['registered', 'waitlist', 'cancelled'])->default('registered');
            $table->boolean('attended')->default(false);
            $table->datetime('check_in_time')->nullable();
            $table->datetime('check_out_time')->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();
            $table->boolean('completed')->default(false);
            $table->integer('score')->nullable(); // if there's an assessment
            $table->boolean('certificate_issued')->default(false);
            $table->string('certificate_number')->nullable();
            $table->date('certificate_date')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('rating')->nullable(); // 1-5
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['training_session_id', 'farmer_id']);
            $table->index('attended');
            $table->index('completed');
        });

        // Training Materials
        Schema::create('training_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('training_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('title_sw')->nullable();
            $table->text('description')->nullable();
            $table->string('type'); // document, video, presentation, handout
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('external_url')->nullable();
            $table->string('language')->default('sw'); // sw, en
            $table->boolean('is_downloadable')->default(true);
            $table->integer('download_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('training_program_id');
            $table->index('type');
        });

        // Training Certificates
        Schema::create('training_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('certificate_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('farmer_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_certificates');
        Schema::dropIfExists('training_materials');
        Schema::dropIfExists('training_attendances');
        Schema::dropIfExists('training_sessions');
        Schema::dropIfExists('training_programs');
    }
};
