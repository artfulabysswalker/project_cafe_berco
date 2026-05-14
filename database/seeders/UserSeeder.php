<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = DB::table('roles')
            ->where('role_name','Admin')
            ->first();

        $staffRole = DB::table('roles')
            ->where('role_name','Staff')
            ->first();

        $customerRole = DB::table('roles')
            ->where('role_name','Customer')
            ->first();

        DB::table('users')->insert([

            [
                'name' => 'Main Admin',
                'username' => 'admin1',
                'email' => 'admin1@email.com',
                'password' => Hash::make('password'),
                'id_role' => $adminRole->id_role
            ],

            [
                'name' => 'Cashier Staff',
                'username' => 'staff1',
                'email' => 'staff1@email.com',
                'password' => Hash::make('password'),
                'id_role' => $staffRole->id_role
            ],

            [
                'name' => 'Customer One',
                'username' => 'user1',
                'email' => 'user1@email.com',
                'password' => Hash::make('password'),
                'id_role' => $customerRole->id_role
            ]

        ]);
    }
}