<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->insert([
            'name' => 'Basic Group',
            'description' => 'Basic',
            'icon' => 'icon.png',
            'default' => 1,
            'organization_id' => '1',
            'privacy_id' => 4
        ]);
    }
}
