<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'vpasika',
            'email' => 'vpasika@svitla.com',
            'password' => Hash::make('secret'),
            'api_token' => str_random(60),
        ]);

        DB::table('users')->insert([
            'name' => 'dmytryk',
            'email' => 'dmytryk@svitla.com',
            'password' => Hash::make('4amtolitet'),
            'api_token' => str_random(60),
        ]);

    }
}
