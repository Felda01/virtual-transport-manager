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

        Permission::create(['name' => 'manage static']);
        Permission::create(['name' => 'manage drivers']);
        Permission::create(['name' => 'manage vehicles']);
        Permission::create(['name' => 'manage garages']);
        Permission::create(['name' => 'manage persons']);
        Permission::create(['name' => 'manage jobs']);
        Permission::create(['name' => 'manage salary']);


        Role::create(['name' => 'admin'])->givePermissionTo('manage static');
        Role::create(['name' => 'owner'])->givePermissionTo(['manage drivers', 'manage vehicles', 'manage garages', 'manage persons', 'manage jobs', 'manage salary']);
        Role::create(['name' => 'driver manager'])->givePermissionTo('manage drivers');
        Role::create(['name' => 'vehicle manager'])->givePermissionTo('manage vehicles');
        Role::create(['name' => 'garage manager'])->givePermissionTo('manage garages');
        Role::create(['name' => 'person manager'])->givePermissionTo('manage persons');
        Role::create(['name' => 'jop manager'])->givePermissionTo('manage jobs');
    }
}
