<?php

namespace App\Listeners;

use App\ElasticSearch\LessonSearch;
use App\Events\ElasticLessonDeleteIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventLessonDeleteIndex
{
    public function __construct(){

    }

    public function handle(ElasticLessonDeleteIndex $event){
        $search = new LessonSearch();
        $search->deleteIndex($event->id);
    }
}
