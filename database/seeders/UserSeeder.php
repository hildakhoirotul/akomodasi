<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'nik' => '000000',
            'name' => 'Admin 00',
            'password'=> Hash::make('000000'),
        ]);
        DB::table('users')->insert([
            'nik' => '111111',
            'name' => 'Admin 01',
            'password' => Hash::make('111111'),
        ]);
    }
}