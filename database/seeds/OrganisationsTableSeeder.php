<?php

use Illuminate\Database\Seeder;

class OrganisationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('organizations')->insert([
            'name' => 'Graspe',
            'description' => 'description',
            'cover'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'icon' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/organizations/1/icon/logo.JPG',
            'default' => 1
        ]);

        DB::table('organizations')->insert([
            'name' => 'Fake organisation',
            'description' => 'description',
             'cover'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'icon' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/group.jpg',
            'default' => 0
        ]);

    }
}
