<?php

use Illuminate\Database\Seeder;

class SkillTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('skills')->insert([
            'name' => 'PHP',
        ]);

        DB::table('skills')->insert([
            'name' => 'MySQL',
        ]);

        DB::table('skills')->insert([
            'name' => 'JS',
        ]);
    }
}
