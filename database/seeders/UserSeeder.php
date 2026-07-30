<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Demo accounts for manual QA — all use the password "password".
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Administrator', 'email' => 'admin@talim.test', 'role' => 'admin'],
            ['name' => 'Aziz Karimov', 'email' => 'teacher@talim.test', 'role' => 'teacher'],
            ['name' => 'Dilnoza Yusupova', 'email' => 'teacher2@talim.test', 'role' => 'teacher'],
            ['name' => 'Bekzod Qodirov', 'email' => 'student@talim.test', 'role' => 'student'],
            ['name' => 'Zilola Saidova', 'email' => 'student2@talim.test', 'role' => 'student'],
            ['name' => 'Jasur Toshev', 'email' => 'student3@talim.test', 'role' => 'student'],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ]
            );

            if (! $user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }
    }
}
