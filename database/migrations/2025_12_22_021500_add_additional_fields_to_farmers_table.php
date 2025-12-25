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
        Schema::table('farmers', function (Blueprint $table) {
            // Location Information
            $table->string('subvillage')->nullable()->after('village_id');

            // Personal Information (Swahili fields)
            $table->string('spouse_name')->nullable()->after('email'); // Na. ya Bw/Bi Shamba
            $table->string('spouse_title')->nullable()->after('spouse_name'); // Bw/Bi Shamba

            // Additional Information (Swahili fields)
            $table->string('land_size_description')->nullable()->after('notes'); // Ukubwa wa eneo
            $table->string('cotton_producers_count')->nullable()->after('land_size_description'); // Idadi ya wazalishaji wa pamba
            $table->string('lead_farmer')->nullable()->after('cotton_producers_count'); // Mkulima Kiongozi
            $table->string('demo_farm')->nullable()->after('lead_farmer'); // Shamba darasa
            $table->string('owns_farming_tools')->nullable()->after('demo_farm'); // Anamiliki zana za Kilimo
            $table->string('last_prohibited_chemicals_use')->nullable()->after('owns_farming_tools'); // Mara ya mwisho kutumia madawa yasiyoruhusiwa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->dropColumn([
                'subvillage',
                'spouse_name',
                'spouse_title',
                'land_size_description',
                'cotton_producers_count',
                'lead_farmer',
                'demo_farm',
                'owns_farming_tools',
                'last_prohibited_chemicals_use',
            ]);
        });
    }
};
