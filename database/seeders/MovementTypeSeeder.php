<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MovementType;

class MovementTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'code' => 'ENTRY',
                'name' => 'Ingreso de stock',
                'icon' => 'mdi-arrow-down-bold',
                'entry' => true,
                'active' => true,
                'order' => 1,
            ], [
                'code' => 'ADJUSTMENT',
                'name' => 'Ajuste de stock',
                'icon' => 'mdi-arrow-up-bold',
                'entry' => false,
                'active' => true,
                'order' => 2,
            ], [
                'code' => 'TRANSFER',
                'name' => 'Transferencia de stock',
                'icon' => 'mdi-swap-horizontal-bold',
                'entry' => null,
                'active' => true,
                'order' => 3,
            ], [
                'code' => 'SELL',
                'name' => 'Venta',
                'icon' => 'mdi-cash-register',
                'entry' => false,
                'active' => false,
                'order' => 4,
            ], [
                'code' => 'CANCEL_SELL',
                'name' => 'Cancelación de venta',
                'icon' => 'mdi-close-box-multiple',
                'entry' => true,
                'active' => false,
                'order' => 5,
            ]
        ];

        foreach($data as $item) {
            MovementType::firstOrCreate([
                'code' => $item['code'],
            ], [
                'name' => $item['name'],
                'icon' => $item['icon'],
                'entry' => $item['entry'],
                'active' => $item['active'],
                'order' => $item['order'],
            ]);
        }
    }
}
