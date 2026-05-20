<?php

namespace Database\Seeders;

use Phaseolies\Database\Migration\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@doppar.test'],
            [
                'name' => 'Doppar Admin',
                'password' => 'password',
                'role' => User::ROLE_ADMIN,
                'status' => true,
            ]
        );

        for ($i = 1; $i <= 12; $i++) {
            User::updateOrCreate(
                ['email' => "editor{$i}@doppar.test"],
                [
                    'name' => fake()->name(),
                    'password' => 'password',
                    'role' => $i % 3 === 0 ? User::ROLE_AUTHOR : User::ROLE_EDITOR,
                    'status' => $i % 4 !== 0,
                ]
            );
        }
    }
}
