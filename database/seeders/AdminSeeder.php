<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::updateOrCreate(['email' => 'admin@aspireapp.com'], [
            'name' => 'Admin',
            'email' => 'admin@aspireapp.com',
            'password' => bcrypt('password'),
            'email_verified_at' => Carbon::now(),
            'role' => 'admin',
        ]);
    }
}
