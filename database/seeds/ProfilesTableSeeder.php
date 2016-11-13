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
            'avatar' => 'https://unsplash.it/200/200',
            'cover' => 'cover.png',
            'bio' => 'User Bio',
            'color' => 'white'

        ]);

        DB::table('profiles')->insert([

            'user_id' => '2',
            'avatar' => 'https://unsplash.it/200/200',
            'cover' => 'cover.png',
            'bio' => 'User Bio',
            'color' => 'white'

        ]);

    }
}