<?php

use Illuminate\Database\Seeder;

class PrivacyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('privacy')->insert([
            'type' => 'Internal',
            'subtype' => 'Open',
        ]);

        DB::table('privacy')->insert([
            'type' => 'Internal',
            'subtype' => 'Closed',
        ]);

        DB::table('privacy')->insert([
            'type' => 'Internal',
            'subtype' => 'Hidden',
        ]);

        DB::table('privacy')->insert([
            'type' => 'External',
            'subtype' => 'Free',
        ]);

        DB::table('privacy')->insert([
            'type' => 'External',
            'subtype' => 'Subscription',
        ]);

        DB::table('privacy')->insert([
            'type' => 'John Doe',
            'subtype' => 'Invite Only',
        ]);

    }
}
