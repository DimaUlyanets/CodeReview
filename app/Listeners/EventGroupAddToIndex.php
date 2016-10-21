<?php

namespace App\Listeners;

use App\ElasticSearch\GroupSearch;
use App\Events\ElasticGroupAddToIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventGroupAddToIndex
{
    public function __construct(){

    }

    public function handle(ElasticGroupAddToIndex $event){
        $search = new GroupSearch();
        $search->addToIndex($event->id,$event->name,$event->thumbnail);
    }
}
