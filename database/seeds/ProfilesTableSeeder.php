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
            'avatar'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/user.svg',
            'cover' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'bio' => 'User Bio',
            'color' => 'white'

        ]);

        DB::table('profiles')->insert([

            'user_id' => '2',
            'avatar'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/user.svg',
                        'cover' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'bio' => 'User Bio',
            'color' => 'white'

        ]);

    }
}