<?php

use Illuminate\Database\Seeder;

class LessonTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lesson_tag')->insert([
            'tag_id' => '1',
            'lesson_id' => '1'
        ]);

        DB::table('lesson_tag')->insert([
            'tag_id' => '2',
            'lesson_id' => '2'
        ]);

    }
}
