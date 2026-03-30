<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'durancristian31306@gmail.com'],
            [
                'name' => 'Cristian Duran',
                'password' => Hash::make('5732988$'),
                'is_admin' => true,
                'is_active' => true,
                'permisos' => ['admin', 'users', 'crm', 'reports'], // Permisos por defecto
            ]
        );
    }
}
