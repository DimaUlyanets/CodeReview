<?php

use Illuminate\Database\Seeder;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tags')->insert([
            'name' => 'Tag 1',
        ]);

        DB::table('tags')->insert([
            'name' => 'Tag 21',
        ]);

        DB::table('tags')->insert([
            'name' => 'Tag 3',
        ]);
    }
}
