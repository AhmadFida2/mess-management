<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for($i=0;$i<150;$i++)
            {
                Member::create([
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'mobile' => fake()->phoneNumber(),
                    'join_date' => fake()->dateTimeBetween('-1 month','now')->format('Y-m-d'),
                    'is_active' => true,
                    'security' => 5000,
                ]);

                for($j=0;$j<150;$j++)
                {
                    Attendance::create([
                        'date' => fake()->dateTimeBetween('-1 month','now')->format('Y-m-d'),
                        'meal' => 'Dinner',
                        'units' => rand(90,180),
                        'member_id' => $i+1,

                    ]);
                }
            }

    }
}
