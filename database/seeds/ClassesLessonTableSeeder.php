<?php

use Illuminate\Database\Seeder;

class ClassesLessonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('classes_lesson')->insert([
            'lesson_id' => '1',
            'classes_id' => '1'
        ]);

        DB::table('classes_lesson')->insert([
            'lesson_id' => '2',
            'classes_id' => '1'
        ]);
    }

}
