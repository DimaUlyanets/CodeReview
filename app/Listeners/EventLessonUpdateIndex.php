<?php

namespace App\Listeners;

use App\ElasticSearch\LessonSearch;
use App\Events\ElasticLessonUpdateIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventLessonUpdateIndex
{
    public function __construct(){

    }

    public function handle(ElasticLessonUpdateIndex $event){
        $search = new LessonSearch();
        $search->updateIndex($event->id, $event->name, $event->thumbnail);
    }
}
