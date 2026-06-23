<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CkdModel;
use App\Models\Component;

class CkdModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            [
                'code'        => 'EV-X1',
                'name'        => 'EV-X1',
                'description' => 'Electric Vehicle X1',
                'components'  => [
                    ['code' => 'BP',  'name' => 'Battery Pack',   'expected_qty' => 1],
                    ['code' => 'MA',  'name' => 'Motor Assembly', 'expected_qty' => 1],
                    ['code' => 'DLH', 'name' => 'Door LH',        'expected_qty' => 1],
                    ['code' => 'DRH', 'name' => 'Door RH',        'expected_qty' => 1],
                    ['code' => 'WHL', 'name' => 'Wheel',          'expected_qty' => 4],
                ],
            ],
            [
                'code'        => 'EV-X2',
                'name'        => 'EV-X2',
                'description' => 'Electric Vehicle X2',
                'components'  => [
                    ['code' => 'BP',  'name' => 'Battery Pack',   'expected_qty' => 1],
                    ['code' => 'MA',  'name' => 'Motor Assembly', 'expected_qty' => 1],
                    ['code' => 'FB',  'name' => 'Front Bumper',   'expected_qty' => 1],
                    ['code' => 'RB',  'name' => 'Rear Bumper',    'expected_qty' => 1],
                    ['code' => 'WHL', 'name' => 'Wheel',          'expected_qty' => 4],
                ],
            ],
        ];

        foreach ($models as $modelData) {
            $components = $modelData['components'];
            unset($modelData['components']);

            $model = CkdModel::create(array_merge($modelData, ['is_active' => true]));

            foreach ($components as $comp) {
                Component::create(array_merge($comp, [
                    'ckd_model_id' => $model->id,
                    'is_active'    => true,
                ]));
            }
        }
    }
}
