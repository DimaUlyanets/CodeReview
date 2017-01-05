<?php

use Illuminate\Database\Seeder;

class LessonSkillTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lesson_skill')->insert([
            'skill_id' => '1',
            'lesson_id' => '1'
        ]);

        DB::table('lesson_skill')->insert([
            'skill_id' => '2',
            'lesson_id' => '1'
        ]);

        DB::table('lesson_skill')->insert([
            'skill_id' => '2',
            'lesson_id' => '2'
        ]);

        DB::table('lesson_skill')->insert([
            'skill_id' => '3',
            'lesson_id' => '2'
        ]);
    }
}
