<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasApiTokens, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'description', 'icon', 'default'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();;
    }

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


}
