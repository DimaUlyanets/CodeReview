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
        'id', 'name', 'description', 'icon', 'default', 'color'
    ];

    public static function createDefaultGroup($organization, $authorId){

        $privacy = Privacy::whereType("External")->where("subtype", "=", "Free")->first();

        Group::create([
                'name' => 'Base group '.$organization->id,
                'description' => 'Base group',
                'icon' => 'icon.jpg',
                'default' => 1,
                'privacy_id' => $privacy->id,
                'author_id' => $authorId,
                'organization_id' => $organization->id
            ]

        );

    }

    /**
     * The organization has many groups.
     */

    public function group()
    {
        return $this->hasMany('App\Group');
    }

    /**
     * The organization belongs to users
     */


    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    /**
     * The tag belongs to many classes.
     */

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

}
