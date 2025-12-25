<?php

namespace Database\Seeders;

use App\Models\Farms\Farm;
use App\Models\Farmers\Farmer;
use App\Models\Location\Region;
use App\Models\Location\District;
use App\Models\Location\Ward;
use App\Models\Location\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapTestSeeder extends Seeder
{
    /**
     * Seed test data for map integration testing.
     * Creates wards, villages, farmers, and farms with various shapes in Tanzania.
     *
     * Farm Shapes:
     * - Rectangular: Most common, easy for ploughing and irrigation
     * - Square: Efficient use of space
     * - Irregular: Shaped by natural features
     * - Circular: Center-pivot irrigation systems
     * - Strip (long and narrow): Traditional farming near rivers
     * - Triangular: Road intersections or land subdivision
     * - L-Shaped: Complex field arrangements
     * - Terraced: Step-like fields on slopes
     */
    public function run(): void
    {
        $this->command->info('Creating test data for map integration...');

        // Get first region and district
        $region = Region::first();
        $district = District::where('region_id', $region->id)->first();

        if (!$region || !$district) {
            $this->command->error('No regions or districts found. Please run LocationSeeder first.');
            return;
        }

        // Create test wards first
        $wardNames = ['Kibaha Mjini', 'Mlandizi', 'Soga'];
        $createdWards = [];
        foreach ($wardNames as $index => $wardName) {
            $ward = Ward::firstOrCreate(
                ['name' => $wardName, 'district_id' => $district->id],
                [
                    'code' => 'WRD-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'is_active' => true,
                ]
            );
            $createdWards[] = $ward;
        }
        $this->command->info('Created ' . count($createdWards) . ' wards.');

        // Create test villages
        $villages = [
            ['name' => 'Kibaha Kuu', 'latitude' => -6.7833, 'longitude' => 38.9167, 'ward_index' => 0],
            ['name' => 'Mlandizi', 'latitude' => -6.8500, 'longitude' => 38.7833, 'ward_index' => 1],
            ['name' => 'Soga', 'latitude' => -6.7000, 'longitude' => 38.8500, 'ward_index' => 2],
        ];

        $createdVillages = [];
        foreach ($villages as $villageData) {
            $ward = $createdWards[$villageData['ward_index']];
            $village = Village::firstOrCreate(
                ['name' => $villageData['name'], 'district_id' => $district->id],
                [
                    'region_id' => $region->id,
                    'ward_id' => $ward->id,
                    'latitude' => $villageData['latitude'],
                    'longitude' => $villageData['longitude'],
                ]
            );
            $createdVillages[] = $village;
        }

        $this->command->info('Created ' . count($createdVillages) . ' villages.');

        // Farm locations with various shapes
        $farmLocations = [
            // 1. RECTANGULAR FARM - Most common shape
            [
                'name' => 'Rectangular Cotton Farm',
                'shape_type' => 'Rectangular',
                'latitude' => -6.7850,
                'longitude' => 38.9200,
                'certification_status' => 'organic',
                'total_area' => 6.0,
                'cultivated_area' => 5.2,
                'boundary' => [
                    [38.9180, -6.7830],
                    [38.9230, -6.7830],
                    [38.9230, -6.7870],
                    [38.9180, -6.7870],
                    [38.9180, -6.7830], // Close polygon
                ],
            ],
            // 2. SQUARE FARM - Efficient use of space
            [
                'name' => 'Square Organic Estate',
                'shape_type' => 'Square',
                'latitude' => -6.8520,
                'longitude' => 38.7850,
                'certification_status' => 'organic',
                'total_area' => 4.0,
                'cultivated_area' => 3.8,
                'boundary' => [
                    [38.7830, -6.8500],
                    [38.7870, -6.8500],
                    [38.7870, -6.8540],
                    [38.7830, -6.8540],
                    [38.7830, -6.8500],
                ],
            ],
            // 3. IRREGULAR FARM - Natural features shape
            [
                'name' => 'Riverside Irregular Farm',
                'shape_type' => 'Irregular',
                'latitude' => -6.7020,
                'longitude' => 38.8520,
                'certification_status' => 'in-conversion',
                'total_area' => 5.5,
                'cultivated_area' => 4.8,
                'boundary' => [
                    [38.8490, -6.6990],
                    [38.8530, -6.6985],
                    [38.8560, -6.7010],
                    [38.8555, -6.7045],
                    [38.8520, -6.7060],
                    [38.8485, -6.7040],
                    [38.8490, -6.6990],
                ],
            ],
            // 4. CIRCULAR FARM - Center-pivot irrigation
            [
                'name' => 'Circular Pivot Farm',
                'shape_type' => 'Circular',
                'latitude' => -6.7900,
                'longitude' => 38.9100,
                'certification_status' => 'organic',
                'total_area' => 7.8,
                'cultivated_area' => 7.5,
                'boundary' => $this->generateCircle(38.9100, -6.7900, 0.012, 24),
            ],
            // 5. STRIP FARM - Long and narrow, near river
            [
                'name' => 'River Strip Farm',
                'shape_type' => 'Strip',
                'latitude' => -6.8600,
                'longitude' => 38.7900,
                'certification_status' => 'in-conversion',
                'total_area' => 3.2,
                'cultivated_area' => 3.0,
                'boundary' => [
                    [38.7850, -6.8580],
                    [38.7950, -6.8585],
                    [38.7955, -6.8610],
                    [38.7855, -6.8605],
                    [38.7850, -6.8580],
                ],
            ],
            // 6. TRIANGULAR FARM - Road intersection
            [
                'name' => 'Triangle Junction Farm',
                'shape_type' => 'Triangular',
                'latitude' => -6.7100,
                'longitude' => 38.8600,
                'certification_status' => 'conventional',
                'total_area' => 2.8,
                'cultivated_area' => 2.5,
                'boundary' => [
                    [38.8570, -6.7060],
                    [38.8640, -6.7100],
                    [38.8590, -6.7140],
                    [38.8570, -6.7060],
                ],
            ],
            // 7. L-SHAPED FARM - Complex arrangement
            [
                'name' => 'L-Shaped Heritage Farm',
                'shape_type' => 'L-Shaped',
                'latitude' => -6.7750,
                'longitude' => 38.9050,
                'certification_status' => 'organic',
                'total_area' => 8.2,
                'cultivated_area' => 7.8,
                'boundary' => [
                    [38.9020, -6.7720],
                    [38.9060, -6.7720],
                    [38.9060, -6.7760],
                    [38.9080, -6.7760],
                    [38.9080, -6.7800],
                    [38.9040, -6.7800],
                    [38.9040, -6.7760],
                    [38.9020, -6.7760],
                    [38.9020, -6.7720],
                ],
            ],
            // 8. PENTAGON FARM - 5-sided plot
            [
                'name' => 'Pentagon Valley Farm',
                'shape_type' => 'Pentagon',
                'latitude' => -6.8450,
                'longitude' => 38.7750,
                'certification_status' => 'organic',
                'total_area' => 5.5,
                'cultivated_area' => 5.0,
                'boundary' => $this->generatePolygon(38.7750, -6.8450, 0.015, 5),
            ],
            // 9. HEXAGON FARM - 6-sided efficient plot
            [
                'name' => 'Hexagon Green Farm',
                'shape_type' => 'Hexagon',
                'latitude' => -6.7650,
                'longitude' => 38.8850,
                'certification_status' => 'in-conversion',
                'total_area' => 6.2,
                'cultivated_area' => 5.8,
                'boundary' => $this->generatePolygon(38.8850, -6.7650, 0.014, 6),
            ],
            // 10. TERRACED FARM - Step-like on slopes
            [
                'name' => 'Terraced Hill Farm',
                'shape_type' => 'Terraced',
                'latitude' => -6.8100,
                'longitude' => 38.8200,
                'certification_status' => 'organic',
                'total_area' => 4.5,
                'cultivated_area' => 4.0,
                'boundary' => [
                    [38.8170, -6.8070],
                    [38.8200, -6.8075],
                    [38.8210, -6.8090],
                    [38.8220, -6.8100],
                    [38.8230, -6.8120],
                    [38.8220, -6.8135],
                    [38.8190, -6.8130],
                    [38.8175, -6.8115],
                    [38.8165, -6.8095],
                    [38.8170, -6.8070],
                ],
            ],
            // 11. CURVED/CRESCENT FARM - Following contour
            [
                'name' => 'Crescent Moon Farm',
                'shape_type' => 'Crescent',
                'latitude' => -6.8300,
                'longitude' => 38.8500,
                'certification_status' => 'organic',
                'total_area' => 3.8,
                'cultivated_area' => 3.5,
                'boundary' => [
                    [38.8460, -6.8270],
                    [38.8490, -6.8280],
                    [38.8520, -6.8300],
                    [38.8530, -6.8330],
                    [38.8520, -6.8350],
                    [38.8490, -6.8340],
                    [38.8470, -6.8320],
                    [38.8460, -6.8290],
                    [38.8460, -6.8270],
                ],
            ],
            // 12. TRAPEZOID FARM - Sloping boundaries
            [
                'name' => 'Trapezoid Sunrise Farm',
                'shape_type' => 'Trapezoid',
                'latitude' => -6.7550,
                'longitude' => 38.8700,
                'certification_status' => 'conventional',
                'total_area' => 4.2,
                'cultivated_area' => 4.0,
                'boundary' => [
                    [38.8680, -6.7520],
                    [38.8730, -6.7530],
                    [38.8720, -6.7580],
                    [38.8670, -6.7570],
                    [38.8680, -6.7520],
                ],
            ],
        ];

        // Farmer names
        $farmerNames = [
            ['first_name' => 'Juma', 'last_name' => 'Mwamba'],
            ['first_name' => 'Amina', 'last_name' => 'Hassan'],
            ['first_name' => 'Peter', 'last_name' => 'Kimaro'],
            ['first_name' => 'Fatima', 'last_name' => 'Salum'],
            ['first_name' => 'John', 'last_name' => 'Makundi'],
            ['first_name' => 'Grace', 'last_name' => 'Mushi'],
            ['first_name' => 'Ibrahim', 'last_name' => 'Omari'],
            ['first_name' => 'Mary', 'last_name' => 'Lukindo'],
            ['first_name' => 'Hassan', 'last_name' => 'Juma'],
            ['first_name' => 'Sarah', 'last_name' => 'Mtui'],
            ['first_name' => 'David', 'last_name' => 'Ngowi'],
            ['first_name' => 'Rose', 'last_name' => 'Kimambo'],
        ];

        DB::beginTransaction();

        try {
            foreach ($farmLocations as $index => $farmData) {
                $village = $createdVillages[$index % count($createdVillages)];
                $farmerData = $farmerNames[$index];

                // Create farmer
                $farmer = Farmer::create([
                    'registration_number' => 'FRM-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                    'first_name' => $farmerData['first_name'],
                    'last_name' => $farmerData['last_name'],
                    'gender' => $index % 2 === 0 ? 'male' : 'female',
                    'phone' => '+255' . rand(700000000, 799999999),
                    'region_id' => $region->id,
                    'district_id' => $district->id,
                    'village_id' => $village->id,
                    'latitude' => $farmData['latitude'],
                    'longitude' => $farmData['longitude'],
                    'status' => 'active',
                    'registration_date' => now()->subMonths(rand(6, 24)),
                    'certification_status' => $farmData['certification_status'],
                    'household_size' => rand(3, 8),
                    'total_land_size' => $farmData['total_area'],
                ]);

                // Create farm
                Farm::create([
                    'farmer_id' => $farmer->id,
                    'code' => 'SHFARM-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'name' => $farmData['name'],
                    'region_id' => $region->id,
                    'district_id' => $district->id,
                    'village_id' => $village->id,
                    'latitude' => $farmData['latitude'],
                    'longitude' => $farmData['longitude'],
                    'boundary_coordinates' => $farmData['boundary'],
                    'total_area' => $farmData['total_area'],
                    'cultivated_area' => $farmData['cultivated_area'],
                    'soil_type' => ['clay', 'loam', 'sandy loam', 'black cotton'][rand(0, 3)],
                    'water_source' => ['rain-fed', 'irrigation', 'mixed', 'borehole'][rand(0, 3)],
                    'terrain' => ['flat', 'hilly', 'sloped', 'valley'][rand(0, 3)],
                    'certification_status' => $farmData['certification_status'],
                    'organic_since' => $farmData['certification_status'] === 'organic' ? now()->subYears(rand(2, 5)) : null,
                    'status' => 'active',
                    'registration_date' => now()->subMonths(rand(6, 24)),
                    'notes' => "Farm shape: {$farmData['shape_type']}",
                ]);

                $this->command->info("Created: {$farmData['name']} ({$farmData['shape_type']})");
            }

            DB::commit();
            $this->command->info('');
            $this->command->info('✓ Successfully created ' . count($farmLocations) . ' farms with various shapes:');
            $this->command->info('  - Rectangular, Square, Irregular, Circular');
            $this->command->info('  - Strip, Triangular, L-Shaped, Pentagon');
            $this->command->info('  - Hexagon, Terraced, Crescent, Trapezoid');
            $this->command->info('');
            $this->command->info('View the farms map at: /farms-map');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error creating test data: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate circular boundary coordinates
     */
    private function generateCircle(float $centerLng, float $centerLat, float $radius, int $points = 24): array
    {
        $coordinates = [];
        for ($i = 0; $i <= $points; $i++) {
            $angle = ($i / $points) * 2 * M_PI;
            $lng = $centerLng + $radius * cos($angle);
            $lat = $centerLat + ($radius * 0.8) * sin($angle); // Slightly adjust for lat/lng ratio
            $coordinates[] = [$lng, $lat];
        }
        return $coordinates;
    }

    /**
     * Generate regular polygon boundary coordinates
     */
    private function generatePolygon(float $centerLng, float $centerLat, float $radius, int $sides): array
    {
        $coordinates = [];
        for ($i = 0; $i <= $sides; $i++) {
            $angle = ($i / $sides) * 2 * M_PI - M_PI / 2; // Start from top
            $lng = $centerLng + $radius * cos($angle);
            $lat = $centerLat + ($radius * 0.8) * sin($angle);
            $coordinates[] = [$lng, $lat];
        }
        return $coordinates;
    }
}
