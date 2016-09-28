<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'description', 'icon', 'default'
    ];

    public static function createDefaultGroup($organization){

        Group::create([
                'name' => 'Base group',
                'description' => 'Base group',
                'icon' => 'icon.jpg',
                'default' => 1,
                'organization_id' => $organization->id
            ]

        );

    }

    /**
     * The organization has many groups.
     */

    public function groups()
    {
        return $this->hasMany('App\Group');
    }

    /**
     * The organization belongs to users
     */


    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();;
    }

}
