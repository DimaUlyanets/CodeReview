<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'description', 'thumbnail', 'group_id', 'is_collaborative', 'author_id', 'cover'
    ];

    protected $softDelete = true;


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
        return $this->belongsToMany('App\User')->withPivot('role');
    }

    /**
     * The classes to many lessons
     */

    public function lessons()
    {
        return $this->belongsToMany('App\Lesson')->withTimestamps();
    }

    /**
     * The tag belongs to many classes.
     */

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

}
