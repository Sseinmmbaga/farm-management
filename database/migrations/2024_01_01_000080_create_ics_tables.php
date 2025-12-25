<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Compliance Standards
        Schema::create('compliance_standards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_sw')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('category'); // organic, fair_trade, environmental, social
            $table->string('certification_body')->nullable();
            $table->integer('version')->default(1);
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('requirements')->nullable(); // array of requirement items
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });

        // Checklist Templates
        Schema::create('inspection_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compliance_standard_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('name_sw')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('inspection_type'); // internal, external, routine, annual
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Checklist Items
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_checklist_id')->constrained()->cascadeOnDelete();
            $table->string('item_number');
            $table->string('category')->nullable();
            $table->text('question');
            $table->text('question_sw')->nullable();
            $table->text('guidance')->nullable(); // help text for inspectors
            $table->enum('response_type', ['yes_no', 'yes_no_na', 'score', 'text', 'numeric'])->default('yes_no');
            $table->json('response_options')->nullable(); // for custom options
            $table->boolean('is_critical')->default(false); // failure means overall failure
            $table->integer('max_score')->nullable();
            $table->integer('weight')->default(1); // for weighted scoring
            $table->boolean('requires_evidence')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('inspection_checklist_id');
            $table->index('is_critical');
        });

        // Inspections (Ukaguzi)
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_checklist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();
            $table->string('inspection_number')->unique();

            // Inspector
            $table->foreignId('inspector_id')->constrained('users')->cascadeOnDelete();
            $table->string('inspector_name');
            $table->string('inspector_organization')->nullable();

            // Schedule
            $table->date('scheduled_date')->nullable();
            $table->date('inspection_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration_minutes')->nullable();

            // Location
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Results
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('result', ['pending', 'passed', 'failed', 'conditional'])->default('pending');
            $table->decimal('total_score', 8, 2)->nullable();
            $table->decimal('max_possible_score', 8, 2)->nullable();
            $table->decimal('percentage_score', 5, 2)->nullable();
            $table->integer('items_checked')->default(0);
            $table->integer('items_compliant')->default(0);
            $table->integer('items_non_compliant')->default(0);
            $table->integer('items_na')->default(0);
            $table->integer('critical_failures')->default(0);

            // Notes
            $table->text('summary')->nullable();
            $table->text('observations')->nullable();
            $table->text('recommendations')->nullable();
            $table->json('images')->nullable();

            // Follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->foreignId('follow_up_inspection_id')->nullable()->constrained('inspections')->nullOnDelete();

            // Signatures
            $table->string('farmer_signature')->nullable();
            $table->string('inspector_signature')->nullable();
            $table->datetime('signed_at')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('farmer_id');
            $table->index('farm_id');
            $table->index('inspector_id');
            $table->index('inspection_date');
            $table->index('status');
            $table->index('result');
        });

        // Inspection Responses
        Schema::create('inspection_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained()->cascadeOnDelete();
            $table->string('response')->nullable(); // yes, no, na, or score value
            $table->boolean('is_compliant')->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('evidence')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();

            $table->unique(['inspection_id', 'checklist_item_id']);
        });

        // Findings (Matokeo)
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspection_response_id')->nullable()->constrained()->nullOnDelete();
            $table->string('finding_number')->unique();
            $table->string('category'); // non_conformity, observation, improvement
            $table->enum('severity', ['minor', 'major', 'critical'])->default('minor');
            $table->text('description');
            $table->text('description_sw')->nullable();
            $table->text('evidence')->nullable();
            $table->json('images')->nullable();
            $table->text('root_cause')->nullable();
            $table->boolean('requires_corrective_action')->default(true);
            $table->date('due_date')->nullable();
            $table->enum('status', ['open', 'in_progress', 'resolved', 'verified', 'closed'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('inspection_id');
            $table->index('severity');
            $table->index('status');
        });

        // Corrective Actions
        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finding_id')->constrained()->cascadeOnDelete();
            $table->string('action_number')->unique();
            $table->text('description');
            $table->text('description_sw')->nullable();
            $table->enum('action_type', ['correction', 'corrective_action', 'preventive_action'])->default('corrective_action');
            $table->date('planned_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->foreignId('responsible_person_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('responsible_person_name')->nullable();
            $table->text('evidence_of_completion')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'verified', 'ineffective'])->default('planned');
            $table->text('verification_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('verified_at')->nullable();
            $table->boolean('is_effective')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('finding_id');
            $table->index('status');
        });

        // Farmer Certifications
        Schema::create('farmer_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('compliance_standard_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number')->nullable();
            $table->enum('status', ['pending', 'in_conversion', 'certified', 'suspended', 'withdrawn'])->default('pending');
            $table->date('application_date')->nullable();
            $table->date('certification_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('last_inspection_date')->nullable();
            $table->foreignId('last_inspection_id')->nullable()->constrained('inspections')->nullOnDelete();
            $table->integer('conversion_year')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['farmer_id', 'compliance_standard_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_certifications');
        Schema::dropIfExists('corrective_actions');
        Schema::dropIfExists('findings');
        Schema::dropIfExists('inspection_responses');
        Schema::dropIfExists('inspections');
        Schema::dropIfExists('checklist_items');
        Schema::dropIfExists('inspection_checklists');
        Schema::dropIfExists('compliance_standards');
    }
};
