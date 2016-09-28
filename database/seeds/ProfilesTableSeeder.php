<?php

use Illuminate\Database\Seeder;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('profiles')->insert([

            'user_id' => '1',
            'avatar' => 'avatar.png',
            'cover' => 'cover.png',
            'bio' => 'User Bio',
            'color' => 'white'

        ]);

        DB::table('profiles')->insert([

            'user_id' => '2',
            'avatar' => 'avatar.png',
            'cover' => 'cover.png',
            'bio' => 'User Bio',
            'color' => 'white'

        ]);

    }
}
