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
            'icon'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/group.jpg',
            'cover' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'default' => 1,
            'organization_id' => '1',
            'privacy_id' => 4,
            'author_id' => 1
        ]);

        DB::table('groups')->insert([
            'name' => 'Engineering Group',
            'description' => 'Custom',
            'icon'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/group.jpg',
            'cover' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'default' => 0,
            'organization_id' => '1',
            'privacy_id' => 4,
            'author_id' => 1
        ]);

        DB::table('groups')->insert([
            'name' => 'Basic Group 2',
            'description' => 'Basic',
            'icon'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/group.jpg',
            'cover' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'default' => 0,
            'organization_id' => '2',
            'privacy_id' => 4,
            'author_id' => 1
        ]);

        DB::table('groups')->insert([
            'name' => 'Engineering Group 2',
            'description' => 'Custom',
            'icon'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/group.jpg',
            'cover' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'default' => 1,
            'organization_id' => '2',
            'privacy_id' => 4,
            'author_id' => 1
        ]);

    }
}
