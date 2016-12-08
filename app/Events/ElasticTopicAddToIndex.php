<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ElasticClassAddToIndex
{
    use InteractsWithSockets, SerializesModels;

    public $id;
    public $name;
    public $followers;

    public function __construct($id,$name,$followers){
        $this->id = $id;
        $this->name = $name;
        $this->$followers = $followers;
    }

    public function broadcastOn(){
        return new PrivateChannel('channel-name');
    }
}
