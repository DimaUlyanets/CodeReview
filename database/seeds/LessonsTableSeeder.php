<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
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
            'lesson_file' => 'http://www.cbu.edu.zm/downloads/pdf-sample.pdf',
            'difficulty' => '44',
            'type' => '0',
            'group_id' => '1',
            'author_id' => '1',
            'views' => 20,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lessons')->insert([
            'name' => 'Geometry',
            'description' => 'Geometry is a branch of mathematics concerned with questions of shape, size, relative position of figures, and the properties of space.',
            'lesson_file' => 'http://www.cbu.edu.zm/downloads/pdf-sample.pdf',
            'difficulty' => '55',
            'group_id' => '2',
            'type' => '0',
            'author_id' => '2',
            'views' => 20,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')

        ]);

        DB::table('lessons')->insert([
            'name' => 'Algebra',
            'description' => 'Algebra is one of the broad parts of mathematics, together with number theory, geometry and analysis. ',
            'lesson_file' => 'http://www.cbu.edu.zm/downloads/pdf-sample.pdf',
            'difficulty' => '25',
            'group_id' => '2',
            'type' => '0',
            'author_id' => '2',
            'views' => 20,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')

        ]);



    }
}
