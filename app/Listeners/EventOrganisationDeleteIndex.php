<?php

namespace App\Listeners;

use App\ElasticSearch\OrganisationSearch;
use App\Events\ElasticOrganisationDeleteIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventOrganisationDeleteIndex
{
    public function __construct(){

    }

    public function handle(ElasticOrganisationDeleteIndex $event){
        $search = new OrganisationSearch();
        $search->deleteIndex($event->id);
    }
}
