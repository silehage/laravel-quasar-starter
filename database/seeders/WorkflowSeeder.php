<?php

namespace Database\Seeders;

use App\Models\WorkflowCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listItems = [
            [
                'name' => 'Produksi Rajut',
                'workflows' => [
                    'Rajut',
                    'Linking',
                    'Obras',
                    'Pasang Label ',
                    'Pasang Resleting',
                    'Steam',
                    'Som',
                    'Packing',
                    'Lead',
                ]
            ],
            [
                'name' => 'Produksi Celana Bandung',
                'workflows' => [
                    'Cutting',
                    'Jahit',
                    'Bordel',
                    'Finsihing',
                ]
            ],
            [
                'name' => 'Produksi Jaket',
                'workflows' => [
                    'Cutting',
                    'Jahit'
                ]
            ],
            [
                'name' => 'Produksi Celana Rajut',
                'workflows' => [
                    'Rajut',
                    'Linking',
                    'Pasang Resleting + Saku',
                    'Obras',
                    'Som + Pasang Tali Tas Serut',
                    'Pasang Label',
                ]
            ]

        ];
        
        foreach($listItems as $list) {
           $cat = WorkflowCategory::firstorCreate([
               'name' => $list['name']
               ], [
                   'name' => $list['name']
            ]);
            
            $sort = 1;
            foreach($list['workflows'] as $flow) {
                $cat->items()->updateOrCreate([
                    'name' => $flow
                ], [
                    'name' => $flow,
                    'sort' => $sort
                ]);

                $sort++;
            }
        }
    }
}
