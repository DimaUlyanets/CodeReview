<?php

namespace App\Listeners;

use App\ElasticSearch\OrganizationSearch;
use App\Events\ElasticOrganizationUpdateIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventOrganizationUpdateIndex
{
    public function __construct(){

    }

    public function handle(ElasticOrganizationUpdateIndex $event){
        $search = new OrganizationSearch();
        $search->updateIndex($event->id,$event->name,$event->thumbnail);
    }
}
