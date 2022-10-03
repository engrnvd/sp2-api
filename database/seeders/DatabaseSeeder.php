<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Naveed',
            'email' => '08es34@gmail.com',
            'password' => bcrypt('123456'),
            'email_verified_at' => Carbon::now(),
        ]);
    }
}
