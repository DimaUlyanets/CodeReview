<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public static function getGroupInfo($group){

        $classes = $group->classes;
        $lessons = $group->lessons;

        $response = [

            "id" => $group->id,
            "name" => $group->name,
            "description" => $group->description,
            "thumbnail" => $group->icon,
            "lessons_num" => $lessons->count(),
            "classes_num" => $classes->count(),

        ];

        $relatedLessons = [];
        foreach($lessons as $key => $value){

            $relatedLessons[$key]["id"] = $value->id;
            $relatedLessons[$key]["thumbnail"] = $value->thumbnail;
            $relatedLessons[$key]["name"] = $value->name;
            $relatedLessons[$key]["author_id"] = $value->author_id;

        }

        $relatedClasses = [];
        foreach($classes as $key => $value){

            $relatedClasses[$key]["id"] = $value->id;
            $relatedClasses[$key]["thumbnail"] = $value->thumbnail;
            $relatedClasses[$key]["name"] = $value->name;
            $relatedClasses[$key]["author_id"] = $value->author_id;

        }

        $response["child"]["lessons"] = $relatedLessons;
        $response["child"]["classes"] = $relatedClasses;

        return $response;

    }

    public static function userHasAccess($group){

        $user =  Auth::guard('api')->user();

        if(!$group) return false;

        $member = $user->groups()->whereId($group->id)->first();
        $privacy = $group->privacy;

        if($member || "{$privacy->type} {$privacy->subtype}" == "External Free"){

            return true;

        }

        return false;

    }

    /**
     * The privacy that belong to the group.
     */
    public function privacy()
    {
        return $this->belongsTo('App\Privacy');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    /**
     * The organization has many groups.
     */

    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }


    /**
     * The group has many classes.
     */

    public function classes()
    {
        return $this->hasMany('App\Classes');
    }

    /**
     * The group has many lessons.
     */

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }

    /**
     * The tag belongs to many classes.
     */

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    /**
     * The organizations that belong to the user.
     */


}
