<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Role Resmi Tersedia
        $adminRole = Role::firstOrCreate(['role_name' => 'Admin']);
        $cashierRole = Role::firstOrCreate(['role_name' => 'Cashier']);
        $customerRole = Role::firstOrCreate(['role_name' => 'Customer']);

        // 2. Akun Resmi Admin / Owner
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Berco / Owner',
                'email' => 'admin@bercocafe.com',
                'password' => Hash::make('adminberco123'),
                'id_role' => $adminRole->id_role,
                'status' => 'active',
            ]
        );

        // 3. Set Akun Resmi Kasir (Dery, Robin, Nikita)
        $officialCashiers = [
            [
                'username' => 'dery',
                'name' => 'Dery',
                'email' => 'dery@bercocafe.com',
                'password' => '123456',
            ],
            [
                'username' => 'robin',
                'name' => 'Robin',
                'email' => 'robin@bercocafe.com',
                'password' => '123456',
            ],
            [
                'username' => 'nikita',
                'name' => 'Nikita',
                'email' => 'nikita@bercocafe.com',
                'password' => '123456',
            ],
        ];

        foreach ($officialCashiers as $cashierData) {
            User::updateOrCreate(
                ['username' => $cashierData['username']],
                [
                    'name' => $cashierData['name'],
                    'email' => $cashierData['email'],
                    'password' => Hash::make($cashierData['password']),
                    'id_role' => $cashierRole->id_role,
                    'status' => 'active',
                ]
            );
        }

        // 4. Akun Pelanggan Resmi (Customer)
        User::updateOrCreate(
            ['username' => 'user1'],
            [
                'name' => 'Customer One',
                'email' => 'user1@bercocafe.com',
                'password' => Hash::make('password'),
                'id_role' => $customerRole->id_role,
                'status' => 'active',
            ]
        );

        // 5. Hapus Akun Dummy Duplikat Lama dengan aman (Reassign FK relasi agar integritas database tetap terjaga)
        $dummyUsers = User::where(function ($q) {
            $q->where('email', 'like', '%@kasir.com')
                ->orWhere('username', 'like', '%@kasir.com')
                ->orWhereIn('email', ['admin1@email.com', 'staff1@email.com', 'dery@email.com', 'user2@email.com', 'staff_test@bercocafe.com', 'user1@email.com']);
        })->whereNotIn('username', ['admin', 'dery', 'robin', 'nikita', 'user1', 'guest'])->get();

        foreach ($dummyUsers as $dummy) {
            // Reassign orders/shifts jika ada relasi
            DB::table('orders')->where('id_user', $dummy->id_user)->update(['id_user' => $admin->id_user]);
            if (DB::getSchemaBuilder()->hasTable('shifts')) {
                DB::table('shifts')->where('id_user', $dummy->id_user)->update(['id_user' => $admin->id_user]);
            }
            if (DB::getSchemaBuilder()->hasTable('cart_items')) {
                DB::table('cart_items')->where('user_id', $dummy->id_user)->delete();
            }
            if (DB::getSchemaBuilder()->hasTable('reviews')) {
                DB::table('reviews')->where('user_id', $dummy->id_user)->delete();
            }
            if (DB::getSchemaBuilder()->hasTable('favorites')) {
                DB::table('favorites')->where('user_id', $dummy->id_user)->delete();
            }
            if (DB::getSchemaBuilder()->hasTable('password_reset_requests')) {
                DB::table('password_reset_requests')->where('id_user', $dummy->id_user)->delete();
            }

            $dummy->delete();
        }
    }
}
