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
            'icon' => 'icon.png',
            'default' => 1
        ]);

        DB::table('organizations')->insert([
            'name' => 'Hack tools',
            'description' => 'description',
            'icon' => 'icon.png',
            'default' => 0
        ]);

    }
}
