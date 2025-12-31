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
        Schema::create('financial_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('loan'); // loan, salary_advance, imprest, other
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('TZS');
            $table->text('purpose');
            $table->string('status')->default('pending'); // pending, approved, rejected, disbursed, repaid, cancelled
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('disbursed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disbursed_at')->nullable();
            $table->text('disbursement_notes')->nullable();
            $table->json('repayment_schedule')->nullable(); // array of due dates and amounts
            $table->decimal('total_repayment_amount', 12, 2)->nullable();
            $table->date('repayment_start_date')->nullable();
            $table->date('repayment_end_date')->nullable();
            $table->integer('repayment_installments')->nullable();
            $table->decimal('amount_repaid', 12, 2)->default(0);
            $table->date('last_repayment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('type');
            $table->index('approved_by');
            $table->index('disbursed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_requests');
    }
};