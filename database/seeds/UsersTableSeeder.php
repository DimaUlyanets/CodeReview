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
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'username@gmail.com',
            'password' => Hash::make('secret'),
            'api_token' => str_random(60),
        ]);

        DB::table('users')->insert([
            'name' => 'Peter Doe',
            'username' => 'peterdoe',
            'email' => 'username2@gmail.com',
            'password' => Hash::make('secret'),
            'api_token' => str_random(60),
        ]);

    }
}
