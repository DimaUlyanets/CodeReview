<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'description', 'icon', 'default', 'organization_id', 'privacy_id'
    ];

    /**
     * The privacy that belong to the group.
     */
    public function privacy()
    {
        return $this->belongsTo('App\Privacy');
    }

    /**
     * The organization has many groups.
     */

    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }


    /**
     * The group has many classes.
     */

    public function classes()
    {
        return $this->hasMany('App\Classes');
    }

    /**
     * The group has many lessons.
     */

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }

}
