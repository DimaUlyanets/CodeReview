<?php

namespace App\Listeners;

use App\ElasticSearch\OrganisationSearch;
use App\Events\ElasticOrganisationUpdateIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventOrganisationUpdateIndex
{
    public function __construct(){

    }

    public function handle(ElasticOrganisationUpdateIndex $event){
        $search = new OrganisationSearch();
        $search->updateIndex($event->id, $event->name, $event->thumbnail);
    }
}
