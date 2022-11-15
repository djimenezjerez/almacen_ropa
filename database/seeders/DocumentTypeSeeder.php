<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Cédula de Identidad',
                'code' => 'CI',
                'order' => 1,
            ], [
                'name' => 'Número de Identificación Tributaria',
                'code' => 'NIT',
                'order' => 2,
            ], [
                'name' => 'Cédula de Identidad de Extranjero',
                'code' => 'CEX',
                'order' => 3,
            ],
        ];

        foreach($data as $item) {
            DocumentType::firstOrCreate([
                'code' => $item['code'],
            ], [
                'name' => $item['name'],
                'order' => $item['order'],
            ]);
        }
    }
}
