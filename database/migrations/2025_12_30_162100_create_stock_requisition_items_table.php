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
        Schema::create('stock_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_requisition_id')->constrained('stock_requisitions')->onDelete('cascade');
            $table->foreignId('stock_item_id')->constrained('stock_items')->onDelete('restrict');
            $table->decimal('quantity_requested', 12, 3);
            $table->decimal('quantity_issued', 12, 3)->nullable();
            $table->string('unit_of_measure', 50)->default('pieces');
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_requisition_items');
    }
};
