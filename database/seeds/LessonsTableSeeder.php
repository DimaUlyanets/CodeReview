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
            'difficulty' => '44',
            'type' => '0',
            'group_id' => '1',
            'author_id' => '1',
            'views' => 20
        ]);

        DB::table('lessons')->insert([
            'name' => 'Lesson 2',
            'description' => 'description',
            'thumbnail' => 'thumbnail.png',
            'lesson_file' => 'avi.avi',
            'difficulty' => '55',
            'group_id' => '1',
            'type' => '1',
            'author_id' => '2',
            'views' => 20
        ]);



    }
}
