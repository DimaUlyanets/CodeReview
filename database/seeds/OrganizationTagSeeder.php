<?php

use Illuminate\Database\Seeder;

class OrganizationTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('organization_tag')->insert([
            'tag_id' => '1',
            'organization_id' => '1'
        ]);

        DB::table('organization_tag')->insert([
            'tag_id' => '2',
            'organization_id' => '2'
        ]);

    }
}
