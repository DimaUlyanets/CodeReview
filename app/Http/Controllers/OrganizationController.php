<?php

namespace App\Http\Controllers;

use App\Events\ElasticOrganisationAddToIndex;
use App\Events\ElasticOrganisationDeleteIndex;
use App\Events\ElasticOrganisationUpdateIndex;
use App\Files;
use App\Group;
use App\Http\Requests\OrganizationCreateRequest;
use App\Organization;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(OrganizationCreateRequest $request)
    {
        $data = $request->all();
        $result = Organization::create($data);
        if(!empty($request->icon)){
            $path = Files::qualityCompress($request->icon, "organizations/{$result->id}/icon");
            $result->icon = $path;
        } else {
            $result->icon = 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/group.jpg'; //TODO: temporary
        }
        if(!empty($request->cover)){
            $path = Files::qualityCompress($request->cover, "organizations/{$result->id}/cover");
            $result->cover = $path;
        } else {
            $result->cover = 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png'; //TODO: temporary
        }
        if(isset($request['color'])){
            $result->color = $request['color'];
        }

        $result->save();
        if($result){
            if($request->tags){
                Tag::assignTag($result, $request);
            }
        }
        $user = Auth::guard('api')->user();
        DB::table('organization_user')->insert(
            ['user_id' => $user->id, 'organization_id' => $result->id ,'role'=>'owner']
        );
        if(is_array($request['members']) && count($request['members']) > 0){
            foreach ($request['members'] as $id) {
                DB::table('organization_user')->insert(
                    ['user_id'=>$id,'organization_id'=>$result->id,'role'=>'member']
                );
            }
        }
        if(is_array($request['admins']) && count($request['admins']) > 0){
            $addAdmins = $request['admins'];
            foreach ($addAdmins as $id) {
                DB::table('organization_user')->insert(
                    ['user_id'=>$id,'organization_id'=>$result->id,'role'=>'admin']
                );
            }
        }
        $userId = Auth::guard('api')->user()->id;
        Organization::createDefaultGroup($result, $userId);

        $orgThumbnail = (isset($result->icon)) ? $result->icon : null;
        event(new ElasticOrganisationAddToIndex($result->id, $result['name'], $orgThumbnail));

        return Response::json($result->toArray(), 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $organization = Organization::with('group.lessons', 'group.classes', 'group.tags', 'group.users')->find($id);
        if ($organization) {
            $orgInfo = [];
            $orgInfo['id']= $organization->id;
            $orgInfo['name']= $organization->name;
            $orgInfo['description']= $organization->description;
            $orgInfo['thumbnail']= $organization->icon;
            $orgInfo['color']= $organization->color;
            $orgInfo['cover']= $organization->cover;
            $orgInfo['tags']= $organization->tags;
            $lessons = [];
            $classes = [];
            $users = [];
            $groups = [];
            foreach ($organization->group as $group) {
                if (!Group::userHasAccess($group)) continue;

                if (!empty($group->lessons)) {
                     $tempLessons = [];
                     foreach($group->lessons as $key => $lesson) {
                         $tempLessons[$key] = $lesson;
                         $author = User::whereId($lesson->author_id)->first();
                         $tempLessons[$key]['authorName'] = $author->name;
                         $tempLessons[$key]['authorThumbnail'] = $author->profile ? $author->profile->avatar : null;
                         $tempLessons[$key]['authorName'] = User::whereId($lesson->author_id)->first()->name;
                     }
                     $lessons = array_merge($lessons, $tempLessons);
                }
                if (!empty($group->classes)) {
                    $tempClasses = [];
                    foreach($group->classes as $key => $class) {
                        $tempClasses[$key] = $class;
                        $author = User::whereId($class->author_id)->first();
                        $tempClasses[$key]['authorName'] = $author->name;
                        $tempClasses[$key]['users'] = $class->users;
                        $tempClasses[$key]['authorThumbnail'] = $author->profile ? $author->profile->avatar : null;
                    }
                    $classes = array_merge($classes, $tempClasses);
                }
                if (!$group->default) {
                    $author = User::whereId($group->author_id)->first();
                    $group['authorName'] = $author->name;
                    $group['authorThumbnail'] = $author->profile ? $author->profile->avatar : null;
                    array_push($groups, $group);
                }
            }
            if (!empty($organization->users)) {
                foreach($organization->users as $key => $user) {
                    $users[$key] = $user;
                    $users[$key]['thumbnail'] = $user->profile['avatar'];
                    unset($users[$key]['profile']);
                }
            }
            $response = $orgInfo;
            $response['lessons'] = $lessons;
            $response['classes'] = $classes;
            $response['users'] = $users; //TODO remove dupes
            $response['groups'] = $groups;
            return Response::json($response, 200);
        } else {
             return response()->json(['error' => 'Not found'], 404);
        }

    }

    public function addMembers(Request $request, $id)
    {
        if (isset($request['userIds'])) {
            $addMembers = $request['userIds'];
            foreach ($addMembers as $idMember) {

                $userRole = DB::table('organization_user')
                    ->select("role")
                    ->where('organization_id', '=', $id)
                    ->where('user_id', '=', $idMember)->get();

                if(($userRole->count()==0)){

                    DB::table('organization_user')->insert(
                        ['user_id' => $idMember, 'organization_id' => $id, 'role' => 'member']
                    );

                }else {
                    $role = $userRole->toArray()[0]->role;
                    if ($role != 'admin' && $role != 'owner') {
                        DB::table('organization_user')->insert(
                            ['user_id' => $idMember, 'organization_id' => $id, 'role' => 'member']
                        );
                    }
                }
            }
        }
    }
    public function deleteMembers(Request $request, $id)
    {
        if (isset($request['userIds'])) {
            $delMembers = $request['userIds'];
            foreach ($delMembers as $idMember) {
                $userRole = DB::table('organization_user')
                    ->select("role")
                    ->where('organization_id', '=', $id)
                    ->where('user_id', '=', $idMember)->get();
                if(($userRole->count()==0)){
                    continue;
                }
                $role = $userRole->toArray()[0]->role;
                if ($role != 'admin' && $role != 'owner') {
                    DB::table('organization_user')->where('user_id' , $idMember)->where('organization_id',$id)->delete();
                }
            }
        }
    }

    private function _excludeDefault($groups) {
        return array_values(array_filter($groups,
                function($group){
                    return !$group['default'];
                }
            ));
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

        $organization = Organization::find($id);
        if(isset($request['name'])){
            if($request['name']!= $organization->name) {
                $orgName = Organization::where('name', $request['name'])->first();
                if ($orgName != null) {
                    return response()->json(['error' => 'name must be unique'], 403);
                }
            }
            $organization->name = $request['name'];
        }
        if(!empty($request->icon)){
            $path = Files::qualityCompress($request->icon, "organizations/{$organization->id}/icon");
            $organization->icon = $path;
        }
        if(!empty($request->cover)){
            $path = Files::qualityCompress($request->cover, "organizations/{$organization->id}/cover");
            $organization->cover = $path;
        }

        if(isset($request['description'])) $organization->description = $request['description'];
        if(isset($request['color'])) $organization->color = $request['color'];
        if(isset($request['tags'])) {
            Tag::assignTag($organization, $request);
        }

        $organization->save();

        if (isset($request['addAdmins']) && is_array($request['addAdmins'])) {
            foreach ($request['addAdmins'] as $userId) {
                DB::table('organization_user')->insert(
                    ['user_id' => $userId, 'organization_id' => $organization->id,'role' => 'admin']
                );
            }
        }
        if (isset($request['removeAdmins']) && is_array($request['removeAdmins'])) {
            foreach ($request['removeAdmins'] as $userId) {
                DB::table('organization_user')->where('user_id', $userId)->where('role','admin')->where('organization_id', $organization->id)->delete();
            }
        }

        $orgThumbnail = (isset($organization->icon)) ? $organization->icon : null;
        event(new ElasticOrganisationUpdateIndex($id, $organization->name, $orgThumbnail));
        return Response::json($organization->toArray(), 200);
    }

    public function searchUsers(Request $request, $id, $query)
    {
        $findQuery = '%'.$query.'%';
        $findUser =  User::where('name','LIKE', $findQuery)->get();
        $responseUsers = [];
        foreach($findUser as $user){
            if(($user->organizations->count())>0) {
                if (count($user->organizations->where('id', $id))>0){
                    $userInfo = [];
                    $userInfo['id']= $user->id;
                    $userInfo['name']= $user->name;
                    if ($user->profile)
                        $userInfo['avatar']= $user->profile->avatar;

                    array_push($responseUsers, $userInfo);
                }
            }
        }
        return Response::json($responseUsers, 200);
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

    public function membership($id, $skip = null, $name = null){


        $user = Auth::guard('api')->user();
        $controlledGroups = [];

        #get all groups if user is org admin
        $groups = $user->organizations()->where(function ($q) {
            $q->where('role', 'admin');
        })->whereOrganizationId($id)->get();


        if(count($groups) == 0){

            #get groups where user is admin or owner
            $groups = $user->groups()->where(function ($q) {
                $q->where('role', 'admin')->orWhere('role', 'owner');
            })->whereOrganizationId($id)->get();

        }

        foreach($groups as $value)$controlledGroups[] = $value->id;

        $groups = Group::with('users')->whereIn('id', $controlledGroups)->get();
        $groupsList = [];
        $usersList = [];
        $relationLists = [];

        foreach($groups as $value){

            $groupsList[$value->id]["id"] = $value->id;
            $groupsList[$value->id]["name"] = $value->name;
            $groupsList[$value->id]["icon"] = $value->icon;

            foreach($value->users as $u){

                $relationLists[$value->id][] = $u->id;

            }

        }

        #get all users from organization
        $organizationUsers = Organization::with('users')->whereId($id)->get();
        foreach($organizationUsers as $value){
            foreach($value->users as $u){
                $usersList[$u->id]["id"] = $u->id;
                $usersList[$u->id]["name"] = $u->name;
                if($u->profile)$usersList[$u->id]["avatar"] = $u->profile->avatar;
            }
        }
        
        $response["groups"] = array_values($groupsList);
        $response["users"] = array_values($usersList);
        $response["relations"] = $relationLists;

        return Response::json($response, 200);


    }

}
