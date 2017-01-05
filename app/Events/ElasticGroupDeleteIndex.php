<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ElasticGroupDeleteIndex
{
    use InteractsWithSockets, SerializesModels;

    public $id;
    public function __construct($id){
        $this->id = $id;
    }

    public function broadcastOn(){
        return new PrivateChannel('channel-name');
    }
}
