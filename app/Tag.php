<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The tag belongs to many groups organizations.
     */
    public function organizations()
    {
        return $this->belongsToMany('App\Organization')->withTimestamps();
    }

    /**
     * The tag belongs to many groups.
     */

    public function groups()
    {
        return $this->belongsToMany('App\Group')->withTimestamps();
    }

    /**
     * The tag belongs to many classes.
     */

    public function classes()
    {
        return $this->belongsToMany('App\Classes')->withTimestamps();
    }

}
