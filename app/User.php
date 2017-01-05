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
        'password', 'remember_token'
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
     * The groups that belong controlled by user.
     */
    public function controlledGroups()
    {
        return $this->groups()->where(function ($q) {
            $q->where('role', 'admin')->orWhere('role', 'owner');
        });

    }

    /**
     * The groups that belong controlled by user.
     */
    public function controlledOrganizations()
    {
        return $this->organizations()->where(function ($q) {
            $q->where('role', 'admin');
        });

    }

    /**
     * The user belongs to many Classes
     */
    public function classes()
    {
        return $this->belongsToMany('App\Classes')->withTimestamps();
    }

     /**
     * The group has many classes.
     */

     public function lessons()
     {
        return $this->hasMany('App\Lesson', 'author_id');
     }

    /**
     * The user has one profile
     */
    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public static function followUser($follower, $user){
        $user->followers()->attach($follower->id, ['follower_id' => $follower->id, 'user_id' => $user->id]);
    }

    public static function unFollowUser($follower, $user){
        $user->followers()->where('follower_id', $follower->id)->detach();
    }

    /**
     * The following many tags.
     */

    public function tags()
    {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }

    /**
     * The user has many followers.
     */

    public function followers()
    {
        return $this->belongsToMany('App\User', 'user_follower')->withTimestamps();
    }

}
