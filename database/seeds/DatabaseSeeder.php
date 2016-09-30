<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(OrganisationsTableSeeder::class);
        $this->call(OrganizationToUsersSeeder::class);
        $this->call(PrivacyTableSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(GroupUserSeeder::class);
        $this->call(ClassTableSeeder::class);
        $this->call(ClassesUsersSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(LessonsTableSeeder::class);
        $this->call(ClassesLessonTableSeeder::class);
        $this->call(TagTableSeeder::class);
        $this->call(ClassTagSeeder::class);
        $this->call(GroupTagSeeder::class);
        $this->call(OrganizationTagSeeder::class);
        $this->call(SkillTableSeeder::class);
        $this->call(LessonSkillTableSeeder::class);




    }
}
