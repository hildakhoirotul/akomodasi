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
            'role' => 'admin',
            'chain' => '000000',
            'password'=> Hash::make('000000'),
        ]);
        DB::table('users')->insert([
            'nik' => '111111',
            'name' => 'Admin 01',
            'role' => 'admin',
            'chain' => '111111',
            'password' => Hash::make('111111'),
        ]);
        DB::table('users')->insert([
            'nik' => '222222',
            'name' => 'Guest',
            'role' => 'guest',
            'chain' => '222222',
            'password' => Hash::make('222222'),
        ]);
        DB::table('users')->insert([
            'nik' => '333333',
            'name' => 'Inspektur 01',
            'role' => 'inspektur',
            'chain' => '333333',
            'password' => Hash::make('333333'),
        ]);
    }
}
