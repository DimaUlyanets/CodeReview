<?php

namespace App\Listeners;

use App\ElasticSearch\UserSearch;
use App\Events\ElasticUserAddToIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventUserAddToIndex
{
    public function __construct(){

    }
    public function handle(ElasticUserAddToIndex $event){
        $search = new UserSearch();
        $search->addToIndex($event->id, $event->name, $event->thumbnail);
    }
}
