<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create(array(
            'name'      => 'Admin',
            'email'     => 'admin@gmail.com',
            'password'  => 'Admin@123',
            'role'      => '1'
        ));
        $user->assignRole('admin');
    }
}
