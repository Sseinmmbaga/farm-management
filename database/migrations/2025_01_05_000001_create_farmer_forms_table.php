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
        Schema::create('farmer_forms', function (Blueprint $table) {
            $table->id();
            $table->string('form_type'); // form1, form2, etc.
            $table->string('form_name');
            $table->string('form_name_sw')->nullable(); // Swahili name
            $table->foreignId('farmer_id')->nullable()->constrained('farmers')->nullOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->nullOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('form_data'); // Store form fields as JSON
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected'])->default('draft');
            $table->text('reviewer_notes')->nullable();
            $table->string('season')->nullable();
            $table->date('form_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['form_type', 'status']);
            $table->index(['farmer_id', 'form_type']);
            $table->index('submitted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmer_forms');
    }
};
