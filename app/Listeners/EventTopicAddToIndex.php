<?php

namespace App\Listeners;

use App\ElasticSearch\TopicSearch;
use App\Events\ElasticTopicAddToIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventTopicAddToIndex
{
    public function __construct(){

    }

    public function handle(ElasticTopicAddToIndex $event){
        $search = new TopicSearch();
        $search->addToIndex($event->id, $event->name, $event->followers);
    }
}
