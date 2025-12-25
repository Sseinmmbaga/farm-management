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
        // Tasks table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('title');
            $table->text('description')->nullable();

            // Task classification
            $table->string('type', 30)->default('other'); // TaskType enum
            $table->string('status', 20)->default('pending'); // TaskStatus enum
            $table->string('priority', 20)->default('medium'); // TaskPriority enum

            // Relationships
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('farmer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();

            // Planning dates
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();

            // Actual dates
            $table->timestamp('actual_start_date')->nullable();
            $table->timestamp('actual_end_date')->nullable();

            // Time estimates
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();

            // Cost tracking
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->text('completion_notes')->nullable();

            // Requirements (JSON arrays)
            $table->json('equipment_required')->nullable();
            $table->json('materials_required')->nullable();

            // Assignment tracking
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

            // Completion tracking
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();

            // Cancellation tracking
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();

            // Recurring tasks
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_pattern')->nullable();

            // Subtasks support
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->nullOnDelete();

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('priority');
            $table->index('type');
            $table->index('planned_start_date');
            $table->index('planned_end_date');
            $table->index(['farm_id', 'status']);
            $table->index(['field_id', 'status']);
            $table->index(['farmer_id', 'status']);
        });

        // Task Assignments table
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();

            // Polymorphic assignee (User, Farmer, etc.)
            $table->string('assignee_type');
            $table->unsignedBigInteger('assignee_id');

            // Assignment tracking
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();

            // Response tracking
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Status
            $table->string('status', 20)->default('pending'); // pending, accepted, rejected, in_progress, completed, cancelled
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Hours tracking
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['assignee_type', 'assignee_id']);
            $table->index('status');
            $table->index(['task_id', 'status']);
        });

        // Labor Records table
        Schema::create('labor_records', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();

            // Relationships
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();

            // Worker info (polymorphic - can be User, Farmer, or external worker)
            $table->string('worker_type')->nullable();
            $table->unsignedBigInteger('worker_id')->nullable();
            $table->string('worker_name')->nullable(); // For external workers without system accounts

            // Labor classification
            $table->string('labor_type', 20)->default('casual'); // LaborType enum

            // Time tracking
            $table->date('work_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('hours_worked', 6, 2);
            $table->decimal('overtime_hours', 6, 2)->nullable()->default(0);
            $table->integer('break_minutes')->nullable()->default(0);

            // Cost tracking
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('overtime_rate', 10, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();

            // Payment status
            $table->string('payment_status', 20)->default('pending'); // pending, approved, paid, cancelled
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_reference')->nullable();

            // Work details
            $table->text('work_description')->nullable();
            $table->text('notes')->nullable();
            $table->string('weather_conditions')->nullable();

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('work_date');
            $table->index('labor_type');
            $table->index('payment_status');
            $table->index(['worker_type', 'worker_id']);
            $table->index(['farm_id', 'work_date']);
            $table->index(['field_id', 'work_date']);
            $table->index(['task_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_records');
        Schema::dropIfExists('task_assignments');
        Schema::dropIfExists('tasks');
    }
};
