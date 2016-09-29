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

}
