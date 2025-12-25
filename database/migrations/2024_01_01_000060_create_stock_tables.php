<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stock Categories
        Schema::create('stock_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_sw')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('stock_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('parent_id');
        });

        // Stock Items (Bidhaa)
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('stock_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_sw')->nullable();
            $table->string('code')->unique();
            $table->string('sku')->nullable();
            $table->text('description')->nullable();

            // Stock Levels
            $table->decimal('quantity_on_hand', 12, 2)->default(0);
            $table->decimal('quantity_reserved', 12, 2)->default(0);
            $table->decimal('quantity_available', 12, 2)->default(0);
            $table->string('unit'); // kg, liters, pieces, bags
            $table->decimal('reorder_level', 12, 2)->default(0);
            $table->decimal('reorder_quantity', 12, 2)->default(0);

            // Pricing
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->string('currency')->default('TZS');

            // Additional
            $table->string('brand')->nullable();
            $table->string('manufacturer')->nullable();
            $table->boolean('is_organic_approved')->default(false);
            $table->boolean('requires_batch_tracking')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();

            // Location
            $table->string('warehouse_location')->nullable();
            $table->string('bin_location')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('is_active');
        });

        // Stock Batches (for batch tracking)
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number');
            $table->decimal('quantity', 12, 2);
            $table->decimal('quantity_remaining', 12, 2);
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('received_date');
            $table->string('supplier')->nullable();
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->enum('status', ['available', 'reserved', 'expired', 'depleted'])->default('available');
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['stock_item_id', 'batch_number']);
            $table->index('status');
            $table->index('expiry_date');
        });

        // Stock Transactions (Miamala)
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_type'); // intake, issuance, distribution, return, adjustment, transfer
            $table->string('reference_number')->unique();
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->datetime('transaction_date');

            // Source/Destination
            $table->string('source')->nullable(); // supplier name, warehouse, etc.
            $table->string('destination')->nullable();
            $table->foreignId('farmer_id')->nullable()->constrained()->nullOnDelete(); // if distributed to farmer
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();

            // Documentation
            $table->string('document_number')->nullable(); // invoice, receipt, etc.
            $table->string('document_type')->nullable();
            $table->text('notes')->nullable();

            // Approval
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('completed');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('stock_item_id');
            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index('farmer_id');
            $table->index('status');
        });

        // Stock Distributions (specific tracking for farmer distributions)
        Schema::create('stock_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();
            $table->string('distribution_type'); // credit, cash, free
            $table->decimal('quantity', 12, 2);
            $table->decimal('value', 12, 2)->nullable();
            $table->boolean('is_repaid')->default(false);
            $table->decimal('amount_repaid', 12, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->text('purpose')->nullable(); // seeding, fertilizer_application, etc.
            $table->string('acknowledgement_signature')->nullable();
            $table->foreignId('distributed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('farmer_id');
            $table->index('season_id');
            $table->index('distribution_type');
        });

        // Stock Requests
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farmer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'fulfilled', 'cancelled'])->default('draft');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->date('needed_by')->nullable();
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();

            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();

            // Fulfillment
            $table->foreignId('fulfilled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('fulfilled_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('requested_by');
        });

        // Stock Request Items
        Schema::create('stock_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_requested', 12, 2);
            $table->decimal('quantity_approved', 12, 2)->nullable();
            $table->decimal('quantity_fulfilled', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('stock_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_request_items');
        Schema::dropIfExists('stock_requests');
        Schema::dropIfExists('stock_distributions');
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('stock_batches');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('stock_categories');
    }
};
