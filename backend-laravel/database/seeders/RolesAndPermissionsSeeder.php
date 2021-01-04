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

        Permission::create(['name' => 'manage static', 'guard_name' => 'api']);
        Permission::create(['name' => 'manage drivers', 'guard_name' => 'api']);
        Permission::create(['name' => 'manage vehicles', 'guard_name' => 'api']);
        Permission::create(['name' => 'manage garages', 'guard_name' => 'api']);
        Permission::create(['name' => 'manage persons', 'guard_name' => 'api']);
        Permission::create(['name' => 'manage jobs', 'guard_name' => 'api']);
        Permission::create(['name' => 'manage salary', 'guard_name' => 'api']);


        Role::create(['name' => 'admin', 'guard_name' => 'api'])->givePermissionTo('manage static');
        Role::create(['name' => 'owner', 'guard_name' => 'api'])->givePermissionTo(['manage drivers', 'manage vehicles', 'manage garages', 'manage persons', 'manage jobs', 'manage salary']);
        Role::create(['name' => 'driver manager', 'guard_name' => 'api'])->givePermissionTo('manage drivers');
        Role::create(['name' => 'vehicle manager', 'guard_name' => 'api'])->givePermissionTo('manage vehicles');
        Role::create(['name' => 'garage manager', 'guard_name' => 'api'])->givePermissionTo('manage garages');
        Role::create(['name' => 'person manager', 'guard_name' => 'api'])->givePermissionTo('manage persons');
        Role::create(['name' => 'job manager', 'guard_name' => 'api'])->givePermissionTo('manage jobs');
    }
}
