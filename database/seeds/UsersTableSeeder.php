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
            'name' => 'Philip Donnellan',
            'username' => 'philipD',
            'email' => 'philip.donnellan@ucd.ie',
            'password' => Hash::make('secret'),
            'api_token' => str_random(60),
        ]);

        DB::table('users')->insert([
            'name' => 'Peter Doe',
            'username' => 'peterdoe',
            'email' => 'a@a.com',
            'password' => Hash::make('a@a.com'),
            'api_token' => str_random(60),
        ]);

    }
}
