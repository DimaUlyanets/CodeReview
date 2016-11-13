<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use  Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token', 'username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public static function LessonAndClassAccess($entity){

        $groupId = $entity->group->id;

        $userInClassGroup = Auth::guard('api')->user()->groups()->whereId($groupId)->first();
        $privacyId = Privacy::whereType('External')->where('subtype', '=', 'Free')->first()->id;
        $freeExternalGroup = Group::wherePrivacyId($privacyId)->whereId($groupId)->first();

        if(!$userInClassGroup && !$freeExternalGroup){

            return false;

        }

        return true;

    }

    /**
     * The organizations that belong to the user.
     */
    public function organizations()
    {
        return $this->belongsToMany('App\Organization')->withTimestamps();
    }

    /**
     * The organizations that belong to the user.
     */
    public function groups()
    {
        return $this->belongsToMany('App\Group')->withTimestamps();
    }

    /**
     * The user belongs to many Classes
     */
    public function classes()
    {
        return $this->belongsToMany('App\Classes')->withTimestamps();;
    }

    /**
     * The user has one profile
     */
    public function profile()
    {
        return $this->hasOne('App\Profile');
    }



}
