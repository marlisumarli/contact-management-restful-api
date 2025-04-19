<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'username' => 'test',
            'password' => Hash::make('test'),
            'name' => 'test',
            'token' => 'test'
        ]);

        DB::table('users')->insert([
            'username' => 'attacker',
            'password' => Hash::make('attacker'),
            'name' => 'attacker',
            'token' => 'attacker'
        ]);
    }
}
