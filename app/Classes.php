<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    //


    /**
     * The class belongs to group.
     */

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

}
