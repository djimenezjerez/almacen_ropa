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
                'entry' => true,
                'active' => true,
                'order' => 1,
            ], [
                'code' => 'ADJUSTMENT',
                'name' => 'Ajuste de stock',
                'entry' => false,
                'active' => true,
                'order' => 2,
            ], [
                'code' => 'TRANSFER',
                'name' => 'Transferencia de stock',
                'entry' => null,
                'active' => true,
                'order' => 3,
            ], [
                'code' => 'SELL',
                'name' => 'Venta',
                'entry' => false,
                'active' => false,
                'order' => 4,
            ], [
                'code' => 'CANCEL_SELL',
                'name' => 'Cancelación de venta',
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
                'entry' => $item['entry'],
                'active' => $item['active'],
                'order' => $item['order'],
            ]);
        }
    }
}
