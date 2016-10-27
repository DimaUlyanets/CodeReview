<?php

namespace App\Listeners;

use App\ElasticSearch\OrganizationSearch;
use App\Events\ElasticOrganizationDeleteIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventOrganizationDeleteIndex
{
    public function __construct(){

    }

    public function handle(ElasticOrganizationDeleteIndex $event){
        $search = new OrganizationSearch();
        $search->deleteIndex($event->id);
    }
}
