<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->insert([
            [
                'role' => '1',
                'name' => 'Apka Budget',
                'email' => 'apkabudget@gmail.com',
                'mobile_no' => '6543234567',
                'image' => 'uploads/admin/admin.png',
                'password' => Hash::make('Raja@997335'),
                'temp_password' => 'Raja@997335',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role' => '1',
                'name' => 'admin',
                'email' => 'ApkaBudget@gmail.com',
                'mobile_no' => '0000000000',
                'image' => 'uploads/admin/admin.png', 
                'password' => Hash::make('Hariom@123'),
                'temp_password' => 'Raja@997353',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}