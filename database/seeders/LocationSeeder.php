<?php

namespace Database\Seeders;

use App\Models\Location\Region;
use App\Models\Location\District;
use App\Models\Location\Ward;
use App\Models\Location\Village;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Key cotton/sesame growing regions in Tanzania
        $regions = [
            [
                'name' => 'Singida',
                'name_sw' => 'Singida',
                'code' => 'SNG',
                'latitude' => -4.8160,
                'longitude' => 34.7500,
                'districts' => [
                    ['name' => 'Singida Urban', 'code' => 'SNGU'],
                    ['name' => 'Singida Rural', 'code' => 'SNGR'],
                    ['name' => 'Iramba', 'code' => 'IRAM'],
                    ['name' => 'Manyoni', 'code' => 'MANY'],
                    ['name' => 'Ikungi', 'code' => 'IKUN'],
                ],
            ],
            [
                'name' => 'Shinyanga',
                'name_sw' => 'Shinyanga',
                'code' => 'SHY',
                'latitude' => -3.6640,
                'longitude' => 33.4260,
                'districts' => [
                    ['name' => 'Shinyanga Urban', 'code' => 'SHYU'],
                    ['name' => 'Shinyanga Rural', 'code' => 'SHYR'],
                    ['name' => 'Kahama', 'code' => 'KAHM'],
                    ['name' => 'Kishapu', 'code' => 'KISH'],
                ],
            ],
            [
                'name' => 'Simiyu',
                'name_sw' => 'Simiyu',
                'code' => 'SMY',
                'latitude' => -3.0333,
                'longitude' => 34.1333,
                'districts' => [
                    ['name' => 'Bariadi', 'code' => 'BARI'],
                    ['name' => 'Busega', 'code' => 'BUSE'],
                    ['name' => 'Itilima', 'code' => 'ITIL'],
                    ['name' => 'Maswa', 'code' => 'MASW'],
                    ['name' => 'Meatu', 'code' => 'MEAT'],
                ],
            ],
            [
                'name' => 'Mwanza',
                'name_sw' => 'Mwanza',
                'code' => 'MWZ',
                'latitude' => -2.5167,
                'longitude' => 32.9000,
                'districts' => [
                    ['name' => 'Mwanza City', 'code' => 'MWZC'],
                    ['name' => 'Ilemela', 'code' => 'ILEM'],
                    ['name' => 'Nyamagana', 'code' => 'NYAM'],
                    ['name' => 'Kwimba', 'code' => 'KWIM'],
                    ['name' => 'Magu', 'code' => 'MAGU'],
                    ['name' => 'Misungwi', 'code' => 'MISU'],
                    ['name' => 'Sengerema', 'code' => 'SENG'],
                    ['name' => 'Ukerewe', 'code' => 'UKER'],
                ],
            ],
            [
                'name' => 'Tabora',
                'name_sw' => 'Tabora',
                'code' => 'TBR',
                'latitude' => -5.0167,
                'longitude' => 32.8000,
                'districts' => [
                    ['name' => 'Tabora Urban', 'code' => 'TBRU'],
                    ['name' => 'Igunga', 'code' => 'IGUN'],
                    ['name' => 'Nzega', 'code' => 'NZEG'],
                    ['name' => 'Sikonge', 'code' => 'SIKO'],
                    ['name' => 'Urambo', 'code' => 'URAM'],
                    ['name' => 'Uyui', 'code' => 'UYUI'],
                    ['name' => 'Kaliua', 'code' => 'KALI'],
                ],
            ],
            [
                'name' => 'Dodoma',
                'name_sw' => 'Dodoma',
                'code' => 'DOD',
                'latitude' => -6.1731,
                'longitude' => 35.7419,
                'districts' => [
                    ['name' => 'Dodoma Urban', 'code' => 'DODU'],
                    ['name' => 'Bahi', 'code' => 'BAHI'],
                    ['name' => 'Chamwino', 'code' => 'CHAM'],
                    ['name' => 'Chemba', 'code' => 'CHEM'],
                    ['name' => 'Kondoa', 'code' => 'KOND'],
                    ['name' => 'Kongwa', 'code' => 'KONG'],
                    ['name' => 'Mpwapwa', 'code' => 'MPWA'],
                ],
            ],
        ];

        foreach ($regions as $regionData) {
            $districts = $regionData['districts'];
            unset($regionData['districts']);

            $region = Region::updateOrCreate(
                ['code' => $regionData['code']],
                array_merge($regionData, ['is_active' => true])
            );

            foreach ($districts as $districtData) {
                District::updateOrCreate(
                    ['code' => $districtData['code']],
                    [
                        'region_id' => $region->id,
                        'name' => $districtData['name'],
                        'name_sw' => $districtData['name'],
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Seeded ' . count($regions) . ' regions with districts.');
    }
}
