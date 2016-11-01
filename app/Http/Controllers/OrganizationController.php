<?php

namespace App\Http\Controllers;

use App\Events\ElasticOrganizationAddToIndex;
use App\Events\ElasticOrganizationDeleteIndex;
use App\Events\ElasticOrganizationUpdateIndex;
use App\Files;
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
                $request->tags = explode(',', $request->tags);
                Tag::assignTag($result, $request);
            }
        }
        $user = Auth::guard('api')->user();
        DB::table('organization_user')->insert(
            ['user_id' => $user->id, 'organization_id' => $result->id ,'role'=>'owner']
        );
        if(isset($request['members'])){
            $addMembers = $request['members'];
            foreach ($addMembers as $id) {
                DB::table('organization_user')->insert(
                    ['user_id'=>$id,'organization_id'=>$result->id,'role'=>'member']
                );
            }
        }
        if(isset($request['admins'])){
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
        event(new ElasticOrganizationAddToIndex($idOrganizationToSearch, $nameOrganizationToSearch, $thumbnailOrganizationToSearch));

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
        $organization = Organization::find($id);
        $orgInfo = [];
        $orgInfo['id']= $organization->id;
        $orgInfo['name']= $organization->name;
        $orgInfo['description']= $organization->description;
        $orgInfo['thumbnail']= $organization->thumbnail;
        $orgInfo['cover']= $organization->cover;
        $organizationGroups = self::_excludeDefault($organization->group->toArray());

        $lessons = [];
        foreach ($organization->group as $group) {
            if(count($group->lessons)>0) {
                $lessonInfo = [];
                foreach($group->lessons as $lesson) {
                    $lessonInfo['id'] = $lesson->id;
                    $lessonInfo['name'] = $lesson->name;
                    $lessonInfo['description'] = $lesson->description;
                    $lessonInfo['thumbnail'] = $lesson->thumbnail;
                    $idAuthor =  $lesson->author_id;
                    $author = User::find($idAuthor);
                    $lessonInfo['author'] = $author->name;
                    array_push($lessons, $lessonInfo);
                }
            }
        }
        $classes = [];
        foreach ($organization->group as $group) {
            if(count($group->classes)>0) {
                $classInfo = [];
                foreach ($group->classes as $class) {
                    $classInfo['id'] =  $class->id;
                    $classInfo['name'] =  $class->name;
                    $classInfo['description'] =  $class->description;
                    $classInfo['thumbnail'] =  $class->thumbnail;
                    $idAuthor =  $class->author_id;
                    $author = User::find($idAuthor);
                    $classInfo['author']=$author->name;
                    array_push($classes, $classInfo);
                }
            }
        }
        $response = $orgInfo;
        $response['lessons'] = $lessons;
        $response['classes'] = $classes;
        $response['groups'] = $organizationGroups;
        return Response::json($response, 200);
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
         event(new ElasticOrganizationUpdateIndex($id,$request->name,$request->thumbnail));
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
            $request['tags'] = explode(',', $request['tags']);
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
        return Response::json($organization->toArray(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        event(new ElasticOrganizationDeleteIndex($id));
    }
}
