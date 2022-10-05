<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Naveed',
            'email' => '08es34@gmail.com',
            'password' => bcrypt('123456'),
            'email_verified_at' => Carbon::now(),
            'is_admin' => true,
        ]);
    }
}
