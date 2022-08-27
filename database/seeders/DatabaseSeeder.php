<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Unguarding models');
        Model::unguard();
        $this->call(CitySeeder::class);
        $this->call(DocumentTypeSeeder::class);
        $this->call(StoreSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(WarehouseSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(SizeTypeSeeder::class);
        $this->call(GenderSeeder::class);
        $this->call(ProductSeeder::class);
    }
}
