<?php

namespace App\Listeners;

use App\ElasticSearch\ClassSearch;
use App\Events\ElasticClassAddToIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventClassAddToIndex
{
    public function __construct(){

    }
    public function handle(ElasticClassAddToIndex $event){
        $search = new ClassSearch();
        $search->addToIndex($event->id,$event->name,$event->thumbnail);
    }
}
