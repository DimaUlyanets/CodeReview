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




}
