<?php

namespace App\Http\Controllers;

use App\Events\ElasticOrganizationAddToIndex;
use App\Events\ElasticOrganizationDeleteIndex;
use App\Events\ElasticOrganizationUpdateIndex;
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
            $result->save();
        } else {
            $result->icon = 'https://unsplash.it/200/200'; //TODO: temporary
        }
        if(!empty($request->cover)){
            $path = Files::qualityCompress($request->cover, "organizations/{$result->id}/cover");
            $result->cover = $path;
            $result->save();
        } else {
            $result->cover = 'https://unsplash.it/200/200'; //TODO: temporary
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

        $idOrganizationToSearch = $result->id;
        $nameOrganizationToSearch = $result['name'];
        $thumbnailOrganizationToSearch = (isset($result->icon)) ? $result->icon : null;
        //event(new ElasticOrganizationAddToIndex($idOrganizationToSearch, $nameOrganizationToSearch, $thumbnailOrganizationToSearch));

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
            $orgInfo['cover']= $organization->cover;
            $organizationGroups = self::_excludeDefault($organization->group->toArray());
            $lessons = [];
            $classes = [];
            $users = [];
            foreach ($organization->group as $group) {
                if(!empty($group->lessons)) {
                     $tempLessons = [];
                     foreach($group->lessons as $key => $lesson) {
                         $tempLessons[$key] = $lesson;
                         $tempLessons[$key]['authorName'] = User::whereId($lesson->author_id)->first()->name;
                     }
                     $lessons = array_merge($lessons, $tempLessons);
                }
                if(!empty($group->classes)) {
                    $tempClasses = [];
                    foreach($group->classes as $key => $class) {
                        $tempClasses[$key] = $class;
                        $tempClasses[$key]['authorName'] = User::whereId($class->author_id)->first()->name;
                    }
                    $classes = array_merge($classes, $tempClasses);
                }
                if(!empty($group->users)) {
                    $tempUsers = [];
                    foreach($group->users as $key => $user) {
                        $tempUsers[$key] = $user;
                        $tempUsers[$key]['thumbnail'] = $user->profile['avatar'];
                        unset($tempUsers[$key]['profile']);
                    }
                    $users = array_merge($users, $tempUsers);
                }
            }
            $response = $orgInfo;
            $response['lessons'] = $lessons;
            $response['classes'] = $classes;
            $response['users'] = $users; //TODO remove dupes
            $response['groups'] = $organizationGroups;
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
                $orgaName = Organization::where('name', $request['name'])->first();
                if ($orgaName != null) {
                    return response()->json(['error' => 'name must be unique'], 403);
                }
            }
            $organization->name = $request['name'];
        }
        isset($request['logo'])?$organization->icon = $request['logo']:"";
        isset($request['cover'])?$organization->cover = $request['cover']:"";
        isset($request['description'])?$organization->description = $request['description']:"";
        isset($request['color'])?$organization->color = $request['color']:"";
        if(isset($request['tags'])){
            Tag::assignTag($organization, $request);
        }
        $organization->save();
        if(isset($request['addAdmins'])){
           $addAdmins = $request['addAdmins'];
            foreach ($addAdmins as $idUser) {
                DB::table('organization_user')->insert(
                    ['user_id' => $idUser, 'organization_id' => $organization->id,'role' => 'admin']
                );
            }
       }
        if(isset($request['removeAdmins'])){
            $removeAdmins  = $request['removeAdmins'];
            foreach ($removeAdmins as $idUser) {
                DB::table('organization_user')->where('user_id',$idUser)->where('role','admin')->where('organization_id',$organization->id)->delete();
            }
        }
        event(new ElasticOrganizationUpdateIndex($id,$request->name,$request->thumbnail));
        return Response::json($organization->toArray(), 200);
    }

    public function searchUsers(Request $request, $id, $query)
    {
        $findQuery = '%'.$query.'%';
        $findUser =  User::where('name','LIKE', $findQuery)->get();
        $responseUsers = [];
        foreach($findUser as $user){
            if(($user->organizations->count())>0) {
                if (count($user->organizations->where('id',$id))>0){
                    $userInfo = [];
                    $userInfo['id']= $user->id;
                    $userInfo['name']= $user->name;
                    $avatar = DB::table('profiles')->select('avatar')->where('user_id','=', $user->id)->get();
                    if($avatar!=null) {
                        $userInfo['avatar'] = $avatar[0]->avatar;
                    }
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
}
