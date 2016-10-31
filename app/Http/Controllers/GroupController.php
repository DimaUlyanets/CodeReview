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
use Illuminate\Support\Facades\DB;

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
        //Need add Role when create group in group_user
        DB::table('group_user')->insert(
            ['user_id' => $user->id, 'group_id' => $group->id]
        );

        if(!empty($request->icon)){

            $path = Files::qualityCompress($request->icon, "organizations/{$data["organization_id"]}/groups/{$group->id}/icon");
            $group->icon = $path;
            $group->save();
        } else {
            $group->icon = 'https://unsplash.it/200/200'; //TODO: temporary
            $group->save();
        }

        if($group){

            if($request->tags){
                $request->tags = explode(',', $request->tags);
                Tag::assignTag($group, $request);
            }
            //Assign user to group
            $user->groups()->attach($group->id);


            //START BUILD  DATA TO SEARCH  (need to add thumbnail data, becouse not implemented!)
            $idGroupToSearch = $group->id;
            $nameGroupToSearch = $data['name'];
            $thumbnailGroupToSearch = (isset($data['thumbnail'])) ? $data['thumbnail'] : null;
            event(new ElasticGroupAddToIndex($idGroupToSearch, $nameGroupToSearch, $thumbnailGroupToSearch));

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
        $group = Group::find($id);

        if(isset($request['name'])){
            $groupName = Group::where('name', $request['name'])->first();
            if($groupName!= null){return response()->json(['error' => 'name must be unique'], 403); }
            $group->name = $request['name'];
        }
        isset($request['thumbnail'])?$group->icon = $request['thumbnail']:"";
        isset($request['cover'])?$group->cover = $request['cover']:"";
        isset($request['description'])?$group->description = $request['description']:"";
        if(isset($request['tags'])){
            $request['tags'] = explode(',', $request['tags']);
            Tag::assignTag($group, $request);
        }
        $group->save();

        $user = Auth::guard('api')->user();
        $userRole = DB::table('group_user')->select('role')->where('user_id', '=', $user->id)->where('group_id', '=', $group->id)->get();
        if(count($userRole)>0) {
            if ($userRole->toArray()[0]->role == "owner") {
                if(isset($request['addAdmins'])){
                    $addAdmins = $request['addAdmins'];
                    foreach ($addAdmins as $idUser) {
                        DB::table('group_user')->insert(
                            ['user_id' => $idUser, 'group_id' => $group->id,'role' => 'admin']
                        );
                    }
                }
                if(isset($request['removeAdmins'])){
                    $removeAdmins  = $request['removeAdmins'];
                    foreach ($removeAdmins as $idUser) {
                        DB::table('group_user')->where('user_id',$idUser)->where('role','admin')->where('group_id',$group->id)->delete();
                    }
                }
            }
        }


        return Response::json($group->toArray(), 200);

        event(new ElasticGroupUpdateIndex($group->id, $group->name, $group->icon));
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
