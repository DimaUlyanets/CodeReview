<?php

namespace App\Listeners;

use App\ElasticSearch\ClassSearch;
use App\Events\ElasticClassUpdateIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventClassUpdateIndex
{
    public function __construct(){

    }

    public function handle(ElasticClassUpdateIndex $event){
        $search = new ClassSearch();
        $search->updateIndex($event->id,$event->name,$event->thumbnail);
    }
}
