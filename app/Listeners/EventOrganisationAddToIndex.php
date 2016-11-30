<?php

namespace App\Listeners;

use App\ElasticSearch\OrganisationSearch;
use App\Events\ElasticOrganisationAddToIndex;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventOrganisationAddToIndex
{
    public function __construct()
    {

    }

    public function handle(ElasticOrganisationAddToIndex $event)
    {
        $search = new OrganisationSearch();
        $search->addToIndex($event->id, $event->name, $event->thumbnail);
    }
}
