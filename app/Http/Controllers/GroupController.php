<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests;
use App\Http\Requests\GroupCreateRequest;
use App\Http\Requests\JoinGroupRequest;
use App\Organization;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class GroupController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {

        $response = array();

        foreach(Group::all() as $key => $value){

            $response[$key]["id"] = $value->id;
            $response[$key]["icon"] = $value->icon;
            $response[$key]["name"] = $value->name;

        }

        return $this->setStatusCode(200)->respondSuccess($response);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(GroupCreateRequest $request)
    {

        $data = $request->all();

        if(!$request->organization_id){

            $organization = Organization::whereDefault(1)->first();
            $data["organization_id"] = $organization->id;

        }

        $group = Group::create($data);

        if($group){

            if($request->tags){

                foreach($request->tags as $value){

                    $tag = Tag::create(["name" => $value]);
                    $group->tags()->attach($tag->id);

                }

            }

            return Response::json($group->toArray(), 200);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::find($id);

        if($group){

            $response = Group::getGroupInfo($group);
            return $this->setStatusCode(200)->respondSuccess($response);

        }

        return $this->setStatusCode(404)->respondWithError("Group Not Found");

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * User join to group
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function join(JoinGroupRequest $request)
    {

        $group = Group::find($request->group_id);
        $privacy = $group->privacy;

       if($privacy->type == "External" && $privacy->subtype == "Free"){

           $user = Auth::guard('api')->user();

           if(!$user){

               return $this->setStatusCode(404)->respondWithError("User Not Found");

           }

           if(!$user->groups()->whereId($request->group_id)->first()){

               #attach to organization if not attached before
               if(!$user->organizations()->whereId($group->organization->id)->first()){

                   $user->organizations()->attach($group->organization->id);

               }


               $user->groups()->attach($group->id);

               return $this->setStatusCode(200)->respondSuccess(
                   array(
                       "user_id" => $user->id,
                       "group_id" => $request->group_id
                   )
               );

           }

           return $this->setStatusCode(409)->respondWithError("Already exists conflict");

       }

        return $this->setStatusCode(409)->respondWithError("Privacy conflict");

    }

    public function leave($id){

        $user = Auth::guard('api')->user();

        if($user->groups()->whereId($id)->whereDefault(0)->first()){

            $user->groups()->detach($id);
            exit;

        }

        return $this->setStatusCode(409)->respondWithError("Conflict");

    }
}
