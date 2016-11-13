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
            'cover'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/organizations/1/cover/blue.JPG',
            'icon' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/organizations/1/icon/logo.JPG',
            'default' => 1
        ]);

        DB::table('organizations')->insert([
            'name' => 'Fake organisation',
            'description' => 'description',
            'icon' => 'https://unsplash.it/200/200',
            'default' => 0
        ]);

    }
}
