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
        'id', 'name', 'description', 'thumbnail', 'lesson_file', 'difficulty', 'group_id', 'user_id'
    ];

    /**
     * The lesson to many classes
     */
    public function classes()
    {
        return $this->belongsToMany('App\Classes')->withTimestamps();
    }

}
