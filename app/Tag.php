<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Events\ElasticTopicAddToIndex;

class Tag extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'cover'
    ];

    public static function assignTag($entity, $request){
        $entity->tags()->detach();

        foreach($request->tags as $value){
            $tag = Tag::whereName($value)->first();
            if(!$tag){
                $tag = Tag::create(["name" => $value, "cover" => "https://unsplash.it/800/200"]);
                event(new ElasticTopicAddToIndex($tag->id, $tag->name, 0, $tag->cover));
            }
            $entity->tags()->attach($tag->id);
        }
    }

    public static function followTag($user, $tag){
        $user->tags()->attach($tag->id);
    }

    public static function unFollowTag($user, $tag){
        $user->tags()->detach($tag->id);
    }

    public static function getTagInfo($tag){

        $groups = $tag->groups;
        $classes = $tag->classes;
        $lessons = $tag->lessons;

        $response = [
            "id" => $tag->id,
            "name" => $tag->name,
            "cover" => $tag->cover,
            "following" =>  !!$tag->users()->whereId(Auth::guard('api')->user()->id)->first()
        ];

        $relatedLessons = [];
        foreach($lessons as $key => $value) {
            $relatedLessons[$key]["id"] = $value->id;
            $relatedLessons[$key]["thumbnail"] = $value->thumbnail;
            $relatedLessons[$key]["name"] = $value->name;
            $relatedLessons[$key]["author_name"] = User::whereId($value->author_id)->first()->name;
            $relatedLessons[$key]["views"] = $value->views;

        }

        $relatedClasses = [];
        foreach($classes as $key => $value) {
            $relatedClasses[$key]["id"] = $value->id;
            $relatedClasses[$key]["thumbnail"] = $value->thumbnail;
            $relatedClasses[$key]["name"] = $value->name;
            $relatedClasses[$key]["author_name"] = User::whereId($value->author_id)->first()->name;

        }

        $relatedGroups = [];
        foreach($groups as $key => $group) {
            $relatedGroups[$key] = $group;
            $relatedGroups[$key]['users'] = $group->users;
            $relatedGroups[$key]['tags'] = $group->tags;
            $relatedGroups[$key]["thumbnail"] = $group->icon;
        }

        $response["groups"] = $relatedGroups;
        $response["lessons"] = $relatedLessons;
        $response["classes"] = $relatedClasses;
        $response["users"] = $tag->users;

        return $response;
    }

    /**
     * The tag belongs to many groups organizations.
     */
    public function organizations()
    {
        return $this->belongsToMany('App\Organization')->withTimestamps();
    }

    /**
     * The tag belongs to many groups.
     */

    public function groups()
    {
        return $this->belongsToMany('App\Group')->withTimestamps();
    }

    /**
     * The tag belongs to many classes.
     */

    public function classes()
    {
        return $this->belongsToMany('App\Classes')->withTimestamps();
    }

    /**
     * The tag belongs to many classes.
     */

    public function lessons()
    {
        return $this->belongsToMany('App\Lesson')->withTimestamps();
    }

    /**
     * The has  many followers.
     */

    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

}
