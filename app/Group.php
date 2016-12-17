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
        'id', 'name', 'description', 'icon', 'default', 'organization_id', 'privacy_id', 'author_id'
    ];

    protected $hidden = [
        'pivot'
    ];

    public static function getGroupInfo($group){

        $classes = $group->classes;
        $users = $group->users;
        $lessons = $group->lessons;

        $response = [

            "id" => $group->id,
            "name" => $group->name,
            "description" => $group->description,
            "thumbnail" => $group->icon,
            "privacy_id" => $group->privacy_id,
            "cover" => $group->cover,
            "lessons_num" => $lessons->count(),
            "users_num" => $users->count(),
            "tags" => $group->tags

        ];

        $relatedLessons = [];
        foreach($lessons as $key => $value){
            $author = User::whereId($value->author_id)->first();
            $relatedLessons[$key] = $value;
            $relatedLessons[$key]["authorName"] = $author->name;
            $relatedLessons[$key]["authorThumbnail"] = $author->profile ? $author->profile->avatar : null;
            $relatedLessons[$key]["views"] = $value->views;

        }

        $relatedClasses = [];
        foreach($classes as $key => $value){
            $author = User::whereId($value->author_id)->first();
            $relatedClasses[$key] = $value;
            $relatedClasses[$key]["authorName"] = $author->name;
            $relatedClasses[$key]["authorThumbnail"] = $author->profile ? $author->profile->avatar : null;

        }

        $relatedUsers = [];
        foreach($users as $key => $user){
            $relatedUsers[$key]["id"] = $user->id;
            $relatedUsers[$key]["avatar"] = $user->profile && $user->profile->avatar;
            $relatedUsers[$key]["name"] = $user->name;
            $relatedUsers[$key]["pivot"] = $user->pivot;

        }

        $response["users"] = $relatedUsers;
        $response["lessons"] = $relatedLessons;
        $response["classes"] = $relatedClasses;

        return $response;

    }

    public static function userHasAccess($group){

        $user =  Auth::guard('api')->user();

        if(!$group) return false;

        $member = $user->groups()->whereId($group->id)->first();
        $orgMember = $user->organizations()->whereId($group->organization_id)->first();
        $privacy = $group->privacy;
        $groupType = "{$privacy->type} {$privacy->subtype}";
        if($member
        || $groupType == "External Free"
        || ($groupType == "Internal Open" && $orgMember)){

            return true;

        }

        return false;

    }

    public static function createRelatedArray($entity){

        $result = [];
        foreach($entity as $key => $item) {
            $result[$key]['id'] = $item->id;
            $result[$key]['name'] = $item->name;
            $result[$key]['description'] = $item->description;
            $result[$key]['thumbnail'] = $item->thumbnail;
            $idAuthor =  $item->author_id;
            $author = User::find($idAuthor);
            $result[$key]['author'] = $author->name;
        }

        return $result;

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
        return $this->belongsToMany('App\User')->withPivot('role');
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

}
