<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'description', 'thumbnail', 'lesson_file', 'difficulty', 'group_id', 'author_id', 'type'
    ];

    /**
     * The lessons has one group .
     */

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    /**
     * The lesson to many classes
     */
    public function classes()
    {
        return $this->belongsToMany('App\Classes')->withTimestamps();
    }

    /**
     * The lesson to many classes
     */
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    /**
     * The lesson to many skills
     */
    public function skills()
    {
        return $this->belongsToMany('App\Skill')->withTimestamps();
    }

    /**
     * The tag belongs to many lessons.
     */

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

}
