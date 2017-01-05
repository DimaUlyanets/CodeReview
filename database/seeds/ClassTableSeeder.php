<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ClassTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('classes')->insert([
            'name' => 'Math',
            'description' => 'Basic',
            'thumbnail'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/lesson.jpg',
            'cover' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'group_id' => 2,
            'is_collaborative' => 0,
            'author_id' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')

        ]);

        DB::table('classes')->insert([
            'name' => 'Class 2',
            'description' => 'Basic',
            'thumbnail'=> 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/lesson.jpg',
            'cover' => 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png',
            'group_id' => 2,
            'is_collaborative' => 1,
            'author_id' => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')

        ]);

    }
}
