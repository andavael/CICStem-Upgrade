<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->delete(); // delete existing admins

        DB::table('admins')->insert([
            [
                'first_name' => 'CICS',
                'middle_name' => null,
                'last_name' => 'Student Council',
                'email' => 'cicsscalangilan@g.batstate-u.edu.ph',
                'password' => Hash::make('cicstem12345'),
                'status' => 'Active',
            ],
        ]);
    }
}
