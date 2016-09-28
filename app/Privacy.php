<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Privacy extends Model
{

    protected $table = 'privacy';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'subtype'
    ];

    /**
     * The privacy that belong to the group.
     */
    public function group()
    {
        return $this->belongsTo('App\Group');
    }
}
