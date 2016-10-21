<?php

namespace App\Listeners;

use App\ElasticSearch\GroupSearch;
use App\Events\ElasticGroupDeleteIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventGroupDeleteIndex
{
    public function __construct(){

    }

    public function handle(ElasticGroupDeleteIndex $event){
        $search = new GroupSearch();
        $search->deleteIndex($event->id);
    }
}
