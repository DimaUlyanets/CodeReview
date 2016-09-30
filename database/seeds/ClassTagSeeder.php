<?php

use Illuminate\Database\Seeder;

class ClassTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('classes_tag')->insert([
            'tag_id' => '1',
            'classes_id' => '1'
        ]);

        DB::table('classes_tag')->insert([
            'tag_id' => '2',
            'classes_id' => '2'
        ]);

        DB::table('classes_tag')->insert([
            'tag_id' => '3',
            'classes_id' => '2'
        ]);


    }
}
