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
            'name' => 'Geometry',
            'description' => 'Geometry is a branch of mathematics concerned with questions of shape, size, relative position of figures, and the properties of space.',
            'thumbnail' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/organizations/1/groups/1/lessons/icon/0a9d6af8343f100e1f2b925929c79347.jpeg',
            'lesson_file' => 'avi.avi',
            'difficulty' => '55',
            'group_id' => '2',
            'type' => '1',
            'author_id' => '2',
            'views' => 20
        ]);



    }
}
