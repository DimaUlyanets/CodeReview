<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'description', 'thumbnail', 'group_id', 'is_collaborative', 'author_id'
    ];


    /**
     * The class belongs to group.
     */

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    /**
     * The class has many users.
     */

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * The classes to many lessons
     */

    public function lessons()
    {
        return $this->belongsToMany('App\Lesson')->withTimestamps();
    }

}
