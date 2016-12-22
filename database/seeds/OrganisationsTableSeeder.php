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
            'icon' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/organizations/1/icon/logo.JPG',
            'default' => 1
        ]);

        DB::table('organizations')->insert([
            'name' => 'Fake organisation',
            'description' => 'description',
            'default' => 0
        ]);

    }
}
