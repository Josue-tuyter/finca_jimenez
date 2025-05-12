<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles si no existen
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $registrador = Role::firstOrCreate(['name' => 'Registrador']);

        // Crear permisos básicos
        $permissions = [
            'view_any_dispatch',
            'view_dispatch',
            'create_dispatch',
            'update_dispatch',
            'delete_dispatch',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos a roles
        $admin->givePermissionTo($permissions);
        $superAdmin->givePermissionTo($permissions);
        $registrador->givePermissionTo(['view_any_dispatch', 'view_dispatch']);
    }
}
