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
            'name' => 'username',
            'email' => 'username@gmail.com',
            'password' => Hash::make('secret'),
            'api_token' => str_random(60),
        ]);

        DB::table('users')->insert([
            'name' => 'username2',
            'email' => 'username2@gmail.com',
            'password' => Hash::make('secret'),
            'api_token' => str_random(60),
        ]);

    }
}
