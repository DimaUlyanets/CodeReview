<?php

use Illuminate\Database\Seeder;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('group_user')->insert([
            'user_id' => '1',
            'group_id' => '1'
        ]);

        DB::table('group_user')->insert([
            'user_id' => '2',
            'group_id' => '1'
        ]);
    }
}
