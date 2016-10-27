<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ElasticOrganizationAddToIndex
{
    use InteractsWithSockets, SerializesModels;

    public $id;
    public $name;
    public $thumbnail;

    public function __construct($id,$name,$thumbnail){
        $this->id = $id;
        $this->name = $name;
        $this->thumbnail = $thumbnail;
    }

    public function broadcastOn(){
        return new PrivateChannel('channel-name');
    }
}
