<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Santiago',
            'email' => 'santiagorodrigoj@gmail.com',
            'number' => '0939785542',
            'password' => bcrypt('12345678'),
        ]);

        // Asegurar que el rol existe antes de asignarlo
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);

        // Asignar el rol al usuario
        $user->assignRole($superAdminRole);
    }

    





}
