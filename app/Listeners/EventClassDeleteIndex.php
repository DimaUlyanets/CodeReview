<?php

namespace App\Listeners;

use App\ElasticSearch\ClassSearch;
use App\Events\ElasticClassDeleteIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventClassDeleteIndex
{
    public function __construct(){

    }

    public function handle(ElasticClassDeleteIndex $event){
        $search = new ClassSearch();
        $search->deleteIndex($event->id);
    }
}
