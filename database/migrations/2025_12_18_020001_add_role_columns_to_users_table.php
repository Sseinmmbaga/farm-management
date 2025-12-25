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
        // First, add role_id as nullable
        Schema::table("users", function (Blueprint $table) {
            $table->foreignId("role_id")->after("phone")->nullable()->constrained("roles")->nullOnDelete();
        });
        
        // Then add status column
        Schema::table("users", function (Blueprint $table) {
            $table->enum("status", ["active", "inactive"])->default("active")->after("role_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropForeign(["role_id"]);
            $table->dropColumn(["role_id", "status"]);
        });
    }
};