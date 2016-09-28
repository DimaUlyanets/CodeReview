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
        'id', 'name', 'description', 'thumbnail', 'group_id', 'is_collaborative'
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
        return $this->hasMany('App\User');
    }

}
