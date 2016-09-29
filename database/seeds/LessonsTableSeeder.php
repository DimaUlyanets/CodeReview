<?php

use Illuminate\Database\Seeder;

class LessonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('lessons')->insert([
            'name' => 'Lesson 1',
            'description' => 'description',
            'thumbnail' => 'thumbnail.png',
            'lesson_file' => 'avi.avi',
            'difficulty' => '112',
            'group_id' => '1',
            'user_id' => '1',
        ]);

        DB::table('lessons')->insert([
            'name' => 'Lesson 2',
            'description' => 'description',
            'thumbnail' => 'thumbnail.png',
            'lesson_file' => 'avi.avi',
            'difficulty' => '112',
            'group_id' => '1',
            'user_id' => '2',
        ]);



    }
}
