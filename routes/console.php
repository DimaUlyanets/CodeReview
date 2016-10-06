<?php

use Illuminate\Foundation\Inspiring;
use App\ElasticSearch\ElasticGenerator;
/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');


Artisan::command('make:elastic', function () {

    $generator = new ElasticGenerator();
    $generator->addClassesToSearch();
    $generator->addGroupsToSearch();
    $generator->addLessonsToSearch();



    $this->comment("Data successfully indexed!");
})->describe('Add all data from Lessons Groups Classes to elastic');



