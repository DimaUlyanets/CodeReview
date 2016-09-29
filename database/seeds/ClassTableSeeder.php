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
            'name' => 'Class 1',
            'description' => 'Basic',
            'thumbnail' => 'icon.png',
            'group_id' => 1,
            'is_collaborative' => 0,
            'author_id' => 1

        ]);

        DB::table('classes')->insert([
            'name' => 'Class 2',
            'description' => 'Basic',
            'thumbnail' => 'icon.png',
            'group_id' => 2,
            'is_collaborative' => 1,
            'author_id' => 2

        ]);

    }
}
