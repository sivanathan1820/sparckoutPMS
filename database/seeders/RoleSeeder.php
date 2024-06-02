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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'manage projects']);
        Permission::create(['name' => 'create projects']);
        Permission::create(['name' => 'edit projects']);
        Permission::create(['name' => 'delete projects']);
        Permission::create(['name' => 'assign tasks']);
        Permission::create(['name' => 'view assigned tasks']);

        // create roles and assign existing permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'project manager']);
        $role->givePermissionTo(['create projects', 'edit projects', 'delete projects', 'assign tasks']);

        $role = Role::create(['name' => 'team member']);
        $role->givePermissionTo('view assigned tasks');
    }
}
