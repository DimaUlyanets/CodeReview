<?php

use Illuminate\Database\Seeder;

class GroupTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('group_tag')->insert([
            'tag_id' => '1',
            'group_id' => '1'
        ]);

        DB::table('group_tag')->insert([
            'tag_id' => '2',
            'group_id' => '2'
        ]);

        DB::table('group_tag')->insert([
            'tag_id' => '3',
            'group_id' => '2'
        ]);
    }
}
