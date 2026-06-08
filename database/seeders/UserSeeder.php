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

        DB::table('users')->updateOrInsert(
            ['username' => 'admin1'],
            [
                'name' => 'Main Admin',
                'email' => 'admin1@email.com',
                'password' => Hash::make('password'),
                'id_role' => $adminRole->id_role
            ]
        );

        DB::table('users')->updateOrInsert(
            ['username' => 'staff1'],
            [
                'name' => 'Cashier Staff',
                'email' => 'staff1@email.com',
                'password' => Hash::make('password'),
                'id_role' => $staffRole->id_role
            ]
        );

        DB::table('users')->updateOrInsert(
            ['username' => 'user1'],
            [
                'name' => 'Customer One',
                'email' => 'user1@email.com',
                'password' => Hash::make('password'),
                'id_role' => $customerRole->id_role
            ]
        );
    }
}