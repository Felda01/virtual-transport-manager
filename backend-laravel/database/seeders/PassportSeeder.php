<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::create([
            "user_id" => null,
            "secret" => config('services.passport.password_client_secret'),
            "name" => "Password Grant",
            "revoked" => 0,
            "password_client" => 1,
            "personal_access_client" => 0,
            "redirect" => config('app.url'),
            'provider' => 'users'
        ]);
    }
}
