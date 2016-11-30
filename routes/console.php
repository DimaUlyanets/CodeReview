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


Artisan::command('elastic:sync', function () {

    $generator = new ElasticGenerator();
    $generator->addClassesToSearch();
    $generator->addGroupsToSearch();
    $generator->addLessonsToSearch();
    $generator->addOrganisationsToSearch();


    $this->comment("Data successfully indexed!");
})->describe('Add all data from Lessons Groups Classes to elastic');



Artisan::command('elastic:clear', function () {

    $generator = new ElasticGenerator();
    $generator->clearIndices();

    $this->comment("Data successfully cleared!");
})->describe('Remove all data from elastic');



