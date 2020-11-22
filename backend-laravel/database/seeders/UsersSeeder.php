<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var User $admin */
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@admin.sk',
            'image' => '',
            'password' => Hash::make('secret'),
            'salary' => 0,
            'company_id' => null
        ]);

        $roleAdmin = Role::findByName('admin');
        if ($roleAdmin) {
            $admin->assignRole($roleAdmin);
        }

    }
}
