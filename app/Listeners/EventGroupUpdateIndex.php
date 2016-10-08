<?php

namespace App\Listeners;

use App\ElasticSearch\GroupSearch;
use App\Events\ElasticGroupUpdateIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventGroupUpdateIndex
{
    public function __construct(){

    }

    public function handle(ElasticClassUpdateIndex $event){
        $search = new GroupSearch();
        $search->updateIndex($event->id,$event->name,$event->thumbnail);
    }
}
