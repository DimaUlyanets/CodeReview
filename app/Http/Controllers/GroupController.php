<?php

namespace App\Http\Controllers;

use App\ElasticSearch\GroupSearch;
use App\Events\ElasticGroupAddToIndex;
use App\Events\ElasticGroupDeleteIndex;
use App\Events\ElasticGroupUpdateIndex;
use App\Files;
use App\Group;
use App\Http\Requests;
use App\Http\Requests\GroupCreateRequest;
use App\Http\Requests\JoinGroupRequest;
use App\Organization;
use App\Privacy;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $user = Auth::guard('api')->user();

        foreach($user->groups as $key => $group){
        if (!$group->default) {
            $response[$group->id]["name"] = $group->name;
            $response[$group->id]["thumbnail"] = $group->icon;
            $response[$group->id]["id"] = $group->id;
           }
        }

        $externalFree = Group::wherePrivacyId(Privacy::whereType('External')->where('subtype', '=', 'Free')->first()->id)->get();
        foreach($externalFree as $key => $value){
            if (!$value->default) {
                $response[$value->id]["name"] = $value->name;
                $response[$value->id]["thumbnail"] = $value->icon;
                $response[$value->id]["id"] = $value->id;
            }
        }

        return $this->setStatusCode(200)->respondSuccess(array_values($response));

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

        }else{

            $data["organization_id"] = $request->organization_id;

        }
        $data["default"] = 0;
        $user = Auth::guard('api')->user();
        $data["author_id"] = $user->id;
        $group = Group::create($data);

        if($group){

            if(!empty($request->icon)){
                $path = Files::qualityCompress($request->icon, "organizations/{$data["organization_id"]}/groups/{$group->id}/icon");
                $group->icon = $path;
            } else {
                $group->icon = 'https://unsplash.it/200/200'; //TODO: temporary
            }

            if(!empty($request->cover)){
                $path = Files::qualityCompress($request->cover, "groups/{$group->id}/cover");
                $group->cover = $path;
            } else {
                $group->cover = 'https://unsplash.it/800/200'; //TODO: temporary
            }

            if($request->tags){
                Tag::assignTag($group, $request);
            }
            $group->save();

            $user->groups()->attach($group->id);

            $thumbnailGroup = (isset($data['icon'])) ? $data['icon'] : null;
            $users = $group->users || [];
            event(new ElasticGroupAddToIndex($group->id, $data['name'], $thumbnailGroup, $group->organization_id, $group->privacy_id, $users));

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

            if(!Group::userHasAccess($group) || $group->default){

                return $this->setStatusCode(403)->respondWithError("Forbidden");

            }
            $userId = Auth::guard('api')->user()->id;
            $response = Group::getGroupInfo($group);
            $response['memberOf'] = !!sizeOf($group->whereHas('users', function($q) use($userId, $group) {
                $q->where('user_id', $userId)
                ->where('group_id', $group->id);
            })->get());

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
        $group = Group::find($id);

        if(!$request->organization_id){
            $organization = Organization::whereDefault(1)->first();
            $organizationId = $organization->id;
        }else{
            $organizationId = $request->organization_id;
        }

        if(isset($request['name'])){
            if($request['name']!= $group->name) {
                $groupName = Group::where('name', $request['name'])->first();
                if ($groupName != null) {
                    return response()->json(['error' => 'name must be unique'], 403);
                }
            }
            $group->name = $request['name'];
        }
        if(!empty($request['icon'])){
            $path = Files::qualityCompress($request['icon'], "organizations/{$organizationId}/groups/{$group->id}/icon");
            $group->icon = $path;
        } else {
            $group->icon = 'https://unsplash.it/200/200'; //TODO: temporary
        }
        if(!empty($request['cover'])){
            $path = Files::qualityCompress($request['cover'], "organizations/{$organizationId}/groups/{$group->id}/cover");
            $group->cover = $path;
        } else {
            $group->cover = 'https://unsplash.it/200/200'; //TODO: temporary
        }

        isset($request['description']) ? $group->description = $request['description'] : "";
        if (isset($request['privacy_id'])) {
            $group->privacy_id = $request['privacy_id'];
        }
        if(isset($request['tags'])){
            Tag::assignTag($group, $request);
        }

        $group->save();
        if(isset($request['addAdmins']) || isset($request['removeAdmins'])) {
            if(isset($request['addAdmins']) && is_array($request['addAdmins'])){
                $addAdmins = $request['addAdmins'];
                foreach ($addAdmins as $idUser) {
                    DB::table('group_user')->insert(
                        ['user_id' => $idUser, 'group_id' => $group->id,'role' => 'admin']
                    );
                }
            }

            if(isset($request['removeAdmins']) && is_array($request['removeAdmins'])){
                $removeAdmins  = $request['removeAdmins'];
                foreach ($removeAdmins as $idUser) {
                    DB::table('group_user')->where('user_id',$idUser)->where('role','admin')->where('group_id',$group->id)->delete();
                }
            }
        }

        $groupThumbnail = (isset($group->icon)) ? $group->icon : null;
        event(new ElasticGroupUpdateIndex($group->id, $group->name, $groupThumbnail, $group->organization_id, $group->privacy_id));

        return Response::json($group->toArray(), 200);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {

        //Need complete method and pass (id)!!!
        event(new ElasticGroupDeleteIndex($id));
    }
    public function addMembers(Request $request, $id){
        $group = Group::find($id);
        if(isset($request['addMembers'])){
            $addMembers = $request['addMembers'];
            foreach ($addMembers as $idUser) {
                DB::table('group_user')->insert(
                    ['user_id' => $idUser, 'group_id' => $group->id,'role' => 'member']
                );
            }
        }
        return Response::json($group->toArray(), 200);
    }
    public function deleteMembers(Request $request, $id){
        $group = Group::find($id);
        if(isset($request['deleteMembers'])){
            $deleteMembers = $request['deleteMembers'];
            foreach ($deleteMembers as $idUser) {
                DB::table('group_user')->where('user_id',$idUser)->where('group_id',$group->id)->where('role','member')->delete();
            }
        }
        return Response::json($group->toArray(), 200);
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

               return $this->setStatusCode(204)->respondSuccess(["No content"]);

           }

           return $this->setStatusCode(409)->respondWithError("Already exists conflict");

       }

        return $this->setStatusCode(409)->respondWithError("Privacy conflict");

    }

    public function leave($id){

        $user = Auth::guard('api')->user();

        if($user->groups()->whereId($id)->whereDefault(0)->first()){

            $user->groups()->detach($id);
            return $this->setStatusCode(204)->respondSuccess(["No content"]);

        }

        return $this->setStatusCode(409)->respondWithError("Conflict");

    }
}
