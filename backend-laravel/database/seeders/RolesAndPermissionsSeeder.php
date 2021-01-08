<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => Permission::MANAGE_STATIC, 'guard_name' => Permission::GUARD]);
        Permission::create(['name' => Permission::MANAGE_DRIVERS, 'guard_name' => Permission::GUARD]);
        Permission::create(['name' => Permission::MANAGE_VEHICLES, 'guard_name' => Permission::GUARD]);
        Permission::create(['name' => Permission::MANAGE_GARAGES, 'guard_name' => Permission::GUARD]);
        Permission::create(['name' => Permission::MANAGE_PERSONS, 'guard_name' => Permission::GUARD]);
        Permission::create(['name' => Permission::MANAGE_JOBS, 'guard_name' => Permission::GUARD]);
        Permission::create(['name' => Permission::MANAGE_SALARY, 'guard_name' => Permission::GUARD]);


        Role::create(['name' => Role::ADMIN, 'guard_name' => Permission::GUARD])->givePermissionTo(Permission::MANAGE_STATIC);
        Role::create(['name' => Role::OWNER, 'guard_name' => Permission::GUARD])->givePermissionTo([Permission::MANAGE_DRIVERS, Permission::MANAGE_VEHICLES, Permission::MANAGE_GARAGES, Permission::MANAGE_PERSONS, Permission::MANAGE_JOBS, Permission::MANAGE_SALARY]);
        Role::create(['name' => Role::DRIVER_MANAGER, 'guard_name' => Permission::GUARD])->givePermissionTo(Permission::MANAGE_DRIVERS);
        Role::create(['name' => Role::VEHICLE_MANAGER, 'guard_name' => Permission::GUARD])->givePermissionTo(Permission::MANAGE_VEHICLES);
        Role::create(['name' => Role::GARAGE_MANAGER, 'guard_name' => Permission::GUARD])->givePermissionTo(Permission::MANAGE_GARAGES);
        Role::create(['name' => Role::PERSON_MANAGER, 'guard_name' => Permission::GUARD])->givePermissionTo(Permission::MANAGE_PERSONS);
        Role::create(['name' => Role::JOB_MANAGER, 'guard_name' => Permission::GUARD])->givePermissionTo(Permission::MANAGE_JOBS);
    }
}
