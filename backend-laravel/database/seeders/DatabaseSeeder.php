<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LanguageSeeder::class,
            LanguageLinesSeeder::class,
            ConstantsSeeder::class,
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class,
            PassportSeeder::class
        ]);
    }
}
