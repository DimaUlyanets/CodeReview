<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public static function assignTag($entity, $request){

        foreach($request->tags as $value){

            $tag = Tag::whereName($value)->first();
            if(!$tag){
                $tag = Tag::create(["name" => $value]);
            }
            $entity->tags()->attach($tag->id);

        }

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
        return $this->belongsToMany('App\Lessons')->withTimestamps();
    }

}
