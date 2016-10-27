<?php

namespace App\Listeners;
use App\ElasticSearch\OrganizationSearch;
use App\Events\ElasticOrganizationAddToIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventOrganizationAddToIndex
{
    public function __construct(){

    }

    public function handle(ElasticOrganizationAddToIndex $event){
        $search = new OrganizationSearch();
        $search->addToIndex($event->id,$event->name,$event->thumbnail);
    }
}
