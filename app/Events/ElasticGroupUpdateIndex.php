<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ElasticGroupUpdateIndex
{
    use InteractsWithSockets, SerializesModels;

    public $id;
    public $name;
    public $thumbnail;

    public function __construct($id,$name,$thumbnail, $orgId, $type){
      $this->id = $id;
      $this->name = $name;
      $this->thumbnail = $thumbnail;
      $this->orgId = $orgId;
      $this->type = $type;
    }

    public function broadcastOn(){
        return new PrivateChannel('channel-name');
    }
}
