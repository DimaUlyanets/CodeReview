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
        $organization = Organization::create($data);
        if(!empty($request->icon)){
            $path = Files::qualityCompress($request->icon, "organizations/{$organization->id}/icon");
            $organization->icon = $path;
        } else {
            $organization->icon = 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/group.jpg'; //TODO: temporary
        }
        if(!empty($request->cover)){
            $path = Files::qualityCompress($request->cover, "organizations/{$organization->id}/cover");
            $organization->cover = $path;
        } else {
            $organization->cover = 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png'; //TODO: temporary
        }
        if(isset($request['color'])){
            $organization->color = $request['color'];
        }

        $organization->save();
        if($organization){
            if($request->tags){
                Tag::assignTag($organization, $request);
            }
        }
        $user = Auth::guard('api')->user();
        DB::table('organization_user')->insert(
            ['user_id' => $user->id, 'organization_id' => $organization->id ,'role'=>'owner']
        );

        $defaultGroup = Organization::createDefaultGroup($organization, $user->id);

        if(is_array($request['members']) && count($request['members']) > 0){
            foreach ($request['members'] as $userId) {
               $organization->users()->attach([$userId => ['role'=>'member']]);
               User::find($userId)->groups()->attach($defaultGroup->id);
            }
        }
        if(is_array($request['admins']) && count($request['admins']) > 0){
            $addAdmins = $request['admins'];
            foreach ($addAdmins as $userId) {
                $organization->users()->attach([$userId => ['role'=>'admin']]);
                User::find($userId)->groups()->attach($defaultGroup->id);
            }
        }

        $orgThumbnail = (isset($organization->icon)) ? $organization->icon : null;
        event(new ElasticOrganisationAddToIndex($organization->id, $organization['name'], $orgThumbnail));

        return Response::json($organization->toArray(), 200);
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
        $defaultGroupId = Group::where('organization_id', $organization->id)->where('default', 1)->first()->id;
        if (isset($request['addAdmins']) && is_array($request['addAdmins'])) {
            foreach ($request['addAdmins'] as $userId) {
                $organization->users()->attach([$userId => ['role'=>'admin']]);
                $user = User::find($userId);
                $user->groups()->attach([$defaultGroupId => ['role'=>'admin']]);
            }
        }
        if (isset($request['removeAdmins']) && is_array($request['removeAdmins'])) {
            foreach ($request['removeAdmins'] as $userId) {
                $organization->users()->detach($userId);
                $user = User::find($userId);
                $user->groups()->detach($defaultGroupId);
            }
        }

        if (isset($request['addMembers']) && is_array($request['addMembers'])) {
            foreach ($request['addMembers'] as $userId) {
               $organization->users()->attach([$userId => ['role'=>'member']]);
               $user = User::find($userId);
               $user->groups()->attach([$defaultGroupId => ['role'=>'member']]);
            }
        }
        if (isset($request['removeMembers']) && is_array($request['removeMembers'])) {
            foreach ($request['removeMembers'] as $userId) {
                  $organization->users()->detach($userId);
                  $user = User::find($userId);
                  $user->groups()->detach($defaultGroupId);
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

    public function membership($id, $skip = 0, $filter = null){


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

        foreach($groups as $value) {
            if($value->default) continue;
            $controlledGroups[] = $value->id;
        }

        $groups = Group::with('users')->whereIn('id', $controlledGroups)->get();
        $groupsList = [];
        $usersList = [];
        $relationLists = [];

        foreach($groups as $value){

            $groupsList[$value->id]["id"] = $value->id;
            $groupsList[$value->id]["name"] = $value->name;
            $groupsList[$value->id]["icon"] = $value->icon;

        }

        #get all users from organization
        $with = 'users';
        if($filter) $with = ['users' => function ($query) use($filter) { $query->where('name', 'like', '%' . $filter. '%'); }];
        $organization = Organization::with($with)->whereId($id)->get();
        foreach($organization as $value){
            foreach($value->users as $u){
                $usersList[$u->id]["id"] = $u->id;
                $usersList[$u->id]["name"] = $u->name;
                if($u->profile)$usersList[$u->id]["avatar"] = $u->profile->avatar;

                foreach($u->groups as $g){
                    if ($g->organization_id === intval($id))
                        $relationLists[$u->id][] = $g->id;

                }
            }
        }
        
        $response["groups"] = array_values($groupsList);
        $response["users"] = array_values($usersList);
        $response["relations"] = $relationLists;

        return Response::json($response, 200);


    }

}
