<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Demo accounts (change or delete before real use)
        $accounts = [
            ['Shop Admin', 'admin@repair.test', 'Admin@12345', User::ROLE_ADMIN],
            ['Tina Technician', 'tech@repair.test', 'Tech@12345', User::ROLE_TECHNICIAN],
            ['Frank Front Desk', 'desk@repair.test', 'Desk@12345', User::ROLE_FRONT_DESK],
        ];

        foreach ($accounts as [$name, $email, $password, $role]) {
            $user = new User(['name' => $name, 'email' => $email, 'password' => $password]);
            $user->role = $role; // role is not mass-assignable, so it is set explicitly
            $user->save();
        }
    }
}
