<?php

use Illuminate\Database\Seeder;

class ClassesUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('classes_user')->insert([
            'user_id' => '1',
            'classes_id' => '1'
        ]);

        DB::table('classes_user')->insert([
            'user_id' => '2',
            'classes_id' => '1'
        ]);
    }
}
