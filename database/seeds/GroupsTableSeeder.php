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
            'default' => 1,
            'organization_id' => '1',
            'privacy_id' => 4,
            'author_id' => 1
        ]);

        DB::table('groups')->insert([
            'name' => 'Engineering Group',
            'description' => 'Custom',
            'default' => 0,
            'organization_id' => '1',
            'privacy_id' => 4,
            'author_id' => 1
        ]);

        DB::table('groups')->insert([
            'name' => 'Basic Group 2',
            'description' => 'Basic',
            'default' => 0,
            'organization_id' => '2',
            'privacy_id' => 4,
            'author_id' => 1
        ]);

        DB::table('groups')->insert([
            'name' => 'Engineering Group 2',
            'description' => 'Custom',
            'default' => 1,
            'organization_id' => '2',
            'privacy_id' => 4,
            'author_id' => 1
        ]);

    }
}
