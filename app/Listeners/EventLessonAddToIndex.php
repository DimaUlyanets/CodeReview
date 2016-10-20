<?php

namespace App\Listeners;

use App\ElasticSearch\LessonSearch;
use App\Events\ElasticLessonAddToIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventLessonAddToIndex
{
    public function __construct(){

    }

    public function handle(ElasticLessonAddToIndex $event){
        $search = new LessonSearch();
        $search->addToIndex($event->id,$event->name,$event->thumbnail);
    }
}
