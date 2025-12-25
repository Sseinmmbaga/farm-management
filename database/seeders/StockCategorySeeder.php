<?php

namespace Database\Seeders;

use App\Models\Stock\StockCategory;
use Illuminate\Database\Seeder;

class StockCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Seeds',
                'name_sw' => 'Mbegu',
                'code' => 'SEED',
                'description' => 'Planting seeds for crops',
                'children' => [
                    ['name' => 'Cotton Seeds', 'name_sw' => 'Mbegu za Pamba', 'code' => 'SEED-COT'],
                    ['name' => 'Sesame Seeds', 'name_sw' => 'Mbegu za Ufuta', 'code' => 'SEED-SES'],
                    ['name' => 'Sunflower Seeds', 'name_sw' => 'Mbegu za Alizeti', 'code' => 'SEED-SUN'],
                ],
            ],
            [
                'name' => 'Fertilizers',
                'name_sw' => 'Mbolea',
                'code' => 'FERT',
                'description' => 'Organic and conventional fertilizers',
                'children' => [
                    ['name' => 'Organic Fertilizer', 'name_sw' => 'Mbolea ya Asili', 'code' => 'FERT-ORG'],
                    ['name' => 'NPK Fertilizer', 'name_sw' => 'Mbolea NPK', 'code' => 'FERT-NPK'],
                    ['name' => 'Urea', 'name_sw' => 'Urea', 'code' => 'FERT-URE'],
                    ['name' => 'Compost', 'name_sw' => 'Mboji', 'code' => 'FERT-COM'],
                ],
            ],
            [
                'name' => 'Pesticides',
                'name_sw' => 'Dawa za Wadudu',
                'code' => 'PEST',
                'description' => 'Pest control products',
                'children' => [
                    ['name' => 'Organic Pesticides', 'name_sw' => 'Dawa za Asili', 'code' => 'PEST-ORG'],
                    ['name' => 'Insecticides', 'name_sw' => 'Dawa za Wadudu', 'code' => 'PEST-INS'],
                    ['name' => 'Fungicides', 'name_sw' => 'Dawa za Kuvu', 'code' => 'PEST-FUN'],
                ],
            ],
            [
                'name' => 'Herbicides',
                'name_sw' => 'Dawa za Magugu',
                'code' => 'HERB',
                'description' => 'Weed control products',
            ],
            [
                'name' => 'Equipment',
                'name_sw' => 'Vifaa',
                'code' => 'EQUIP',
                'description' => 'Farm tools and equipment',
                'children' => [
                    ['name' => 'Hand Tools', 'name_sw' => 'Vifaa vya Mkono', 'code' => 'EQUIP-HND'],
                    ['name' => 'Sprayers', 'name_sw' => 'Vinyunyizio', 'code' => 'EQUIP-SPR'],
                    ['name' => 'Protective Gear', 'name_sw' => 'Vifaa vya Kujikinga', 'code' => 'EQUIP-PRO'],
                ],
            ],
            [
                'name' => 'Packaging',
                'name_sw' => 'Vifungashio',
                'code' => 'PACK',
                'description' => 'Packaging materials',
                'children' => [
                    ['name' => 'Bags', 'name_sw' => 'Mifuko', 'code' => 'PACK-BAG'],
                    ['name' => 'Tags & Labels', 'name_sw' => 'Lebo', 'code' => 'PACK-TAG'],
                ],
            ],
            [
                'name' => 'Office Supplies',
                'name_sw' => 'Vifaa vya Ofisi',
                'code' => 'OFFC',
                'description' => 'Office and administrative supplies',
            ],
        ];

        $sortOrder = 0;
        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = StockCategory::updateOrCreate(
                ['code' => $categoryData['code']],
                array_merge($categoryData, [
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ])
            );

            foreach ($children as $childData) {
                StockCategory::updateOrCreate(
                    ['code' => $childData['code']],
                    array_merge($childData, [
                        'parent_id' => $parent->id,
                        'is_active' => true,
                        'sort_order' => $sortOrder++,
                    ])
                );
            }
        }

        $this->command->info('Seeded stock categories.');
    }
}
