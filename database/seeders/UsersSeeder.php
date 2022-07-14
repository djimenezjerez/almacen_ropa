<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\City;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'identity_card' => 9876543210,
                'first_name' => 'SUPER',
                'last_name' => 'ADMINISTRADOR',
                'phone' => 76543210,
                'email' => 'ADMINISTRADOR@ropa.com',
                'city' => 'SC',
                'role' => 'ADMINISTRADOR',
            ],
        ];

        foreach($users as $user) {
            $city = City::where('code', $user['city'])->firstOrFail();
            $role = Role::where('name', $user['role'])->firstOrFail();

            $new_user = User::firstOrCreate([
                'identity_card' => $user['identity_card'],
            ], [
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'password' => $user['identity_card'],
                'phone' => $user['phone'],
                'email' => $user['email'],
                'city_id' => $city->id,
                'role_id' => $role->id,
            ]);
            $new_user->syncRoles([$role->id]);
        }
    }
}
