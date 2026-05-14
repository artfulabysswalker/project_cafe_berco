<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RoleSeeder extends Seeder
{
public function run()
{
    DB::table('roles')->insert([
        ['role_name' => 'Admin'],
        ['role_name' => 'Staff'],
        ['role_name' => 'Guest'],
        ['role_name' => 'Customer'], // ✅ FIXED TYPO
    ]);
}
}