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
            ['email' => 'admin@doppar.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role' => User::ROLE_ADMIN,
                'status' => true,
            ]
        );

        // A small pool of non-admin users to act as commenters/likers, standing
        // in for real visitors who would normally sign up via Google OAuth.
        $commenters = [
            ['name' => 'Ayesha Rahman', 'email' => 'ayesha.rahman@example.com'],
            ['name' => 'Tanvir Ahmed', 'email' => 'tanvir.ahmed@example.com'],
            ['name' => 'Nusrat Jahan', 'email' => 'nusrat.jahan@example.com'],
            ['name' => 'Farhan Kabir', 'email' => 'farhan.kabir@example.com'],
            ['name' => 'Shirin Akter', 'email' => 'shirin.akter@example.com'],
            ['name' => 'Imran Hossain', 'email' => 'imran.hossain@example.com'],
        ];

        foreach ($commenters as $commenter) {
            User::firstOrCreate(
                ['email' => $commenter['email']],
                [
                    'name' => $commenter['name'],
                    'password' => 'password',
                    'role' => User::ROLE_AUTHOR,
                    'status' => true,
                ]
            );
        }
    }
}
