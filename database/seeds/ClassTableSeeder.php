<?php

use Illuminate\Database\Seeder;

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
            'thumbnail' => 'https://unsplash.it/200/200',
            'group_id' => 2,
            'is_collaborative' => 0,
            'author_id' => 1

        ]);

        DB::table('classes')->insert([
            'name' => 'Class 2',
            'description' => 'Basic',
            'thumbnail' => 'https://unsplash.it/200/200',
            'group_id' => 2,
            'is_collaborative' => 1,
            'author_id' => 2

        ]);

    }
}
