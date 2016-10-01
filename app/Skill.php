<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function lessons()
    {
        return $this->belongsToMany('App\Lessons')->withTimestamps();
    }

}
