<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{

    public function lessons()
    {
        return $this->belongsToMany('App\Lessons')->withTimestamps();
    }

}
