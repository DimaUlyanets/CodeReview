<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'avatar', 'cover', 'bio', 'color'
    ];

    /**
     * The profile belongs to one user.
     */

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
