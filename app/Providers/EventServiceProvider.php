<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ElasticClassAddToIndex' => [
            'App\Listeners\EventClassAddToIndex',
        ],
        'App\Events\ElasticClassUpdateIndex' => [
            'App\Listeners\EventClassUpdateIndex',
        ],
        'App\Events\ElasticClassDeleteIndex' => [
            'App\Listeners\EventClassDeleteIndex',
        ],
        'App\Events\ElasticGroupAddToIndex' => [
            'App\Listeners\EventGroupAddToIndex',
        ],
        'App\Events\ElasticGroupUpdateIndex' => [
            'App\Listeners\EventGroupUpdateIndex',
        ],
        'App\Events\ElasticGroupDeleteIndex' => [
            'App\Listeners\EventGroupDeleteIndex',
        ],
        'App\Events\ElasticLessonAddToIndex' => [
            'App\Listeners\EventLessonAddToIndex',
        ],
        'App\Events\ElasticLessonUpdateIndex' => [
            'App\Listeners\EventLessonUpdateIndex',
        ],
        'App\Events\ElasticLessonDeleteIndex' => [
            'App\Listeners\EventLessonDeleteIndex',
        ],
        'App\Events\ElasticOrganisationAddToIndex' => [
            'App\Listeners\EventOrganisationAddToIndex',
        ],
        'App\Events\ElasticOrganisationUpdateIndex' => [
            'App\Listeners\EventOrganisationUpdateIndex',
        ],
        'App\Events\ElasticOrganisationDeleteIndex' => [
            'App\Listeners\EventOrganisationDeleteIndex',
        ],
        'App\Events\ElasticUserAddToIndex' => [
            'App\Listeners\EventUserAddToIndex',
        ],
        'App\Events\ElasticTopicAddToIndex' => [
            'App\Listeners\EventTopicAddToIndex',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
