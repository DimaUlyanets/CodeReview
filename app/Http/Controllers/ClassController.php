<?php

namespace App\Http\Controllers;

use App\Classes;
use App\Files;
use App\Group;
use App\Events\ElasticClassAddToIndex;
use App\Events\ElasticClassUpdateIndex;
use App\Http\Requests\ClassCreateRequest;
use App\Http\Requests\JoinClassRequest;
use App\Organization;
use App\Privacy;
use App\Tag;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class ClassController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(ClassCreateRequest $request)
    {

        $data = (array)$request->all();

        if(!$request->group_id){

            $user = Auth::guard('api')->user();
            $data["group_id"] = $user->organizations()->whereId($request->organization_id)->first()->group()->whereDefault(1)->first()->id;

        }else{

            $data["group_id"] = $request->group_id;

            $relation = Organization::checkToConflict($request->group_id, $request->organization_id);
            if(!$relation){
                return $this->setStatusCode(409)->respondWithError("Group not related to Organization");
            }

        }

        $user = Auth::guard('api')->user();
        $data["author_id"] = $user->id;

        $class = Classes::create($data);

        if ($class){

            $organization = Group::find($data["group_id"])->organization;
            if(!empty($request->thumbnail)){
                $class->thumbnail = Files::qualityCompress($request->thumbnail, "organizations/{$organization->id}/groups/{$data["group_id"]}/classes/{$class->id}/icon");
            } else {
                $class->thumbnail = 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/lesson.jpg'; //TODO: temporary
            }

            if(!empty($request->cover)){
                $path = Files::qualityCompress($request->cover, "organizations/{$organization->id}/classes/{$class->id}/cover");
                $class->cover = $path;
            } else {
                $class->cover = 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png'; //TODO: temporary
            }

            if($request->tags){
                Tag::assignTag($class, $request);
            }

            if(is_array($request['members']) && count($request['members']) > 0){
                foreach ($request['members'] as $id) {
                    DB::table('classes_user')->insert(
                        ['user_id'=>$id,'classes_id'=>$class->id,'role'=>'member']
                    );
                }
            }

            if(is_array($request['admins']) && count($request['admins']) > 0){
                $addAdmins = $request['admins'];
                foreach ($addAdmins as $id) {
                    DB::table('classes_user')->insert(
                        ['user_id'=>$id,'classes_id'=>$class->id,'role'=>'admin']
                    );
                }
            }

            $class->save();
            //Assign user to class
            $user->classes()->attach($class->id);

            $idClassToSearch = $class->id;
            $nameClassToSearch = $data['name'];
            $thumbnailClassToSearch = (isset($class['thumbnail'])) ? $class['thumbnail'] : null;
            event(new ElasticClassAddToIndex($idClassToSearch, $nameClassToSearch, $thumbnailClassToSearch));
            return Response::json($class->toArray(), 200);

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
        $class = Classes::find($id);

        if($class){
            $userId = Auth::guard('api')->user()->id;
            if(!User::LessonAndClassAccess($class))return $this->setStatusCode(403)->respondWithError("Forbidden");

            $user = User::find($class->author_id);

            $lessons = [];
            foreach($class->lessons as $key => $value){

                $lessons[$key]["id"] = $value->id;
                $lessons[$key]["name"] = $value->name;
                $lessons[$key]["thumbnail"] = $value->thumbnail;
                $lessons[$key]["author_name"] = $value->author->name;
                $lessons[$key]["views"] = $value->views;

            }

            $avatar = ($user->profile) ? $user->profile->avatar : "";
            $isMember = $class->whereHas('users', function($q) use($userId, $class) {
                $q->where('user_id', $userId)
                ->where('classes_id', $class->id);
            })->get();
            $users = [];
            if (!empty($class->users)) {
                foreach($class->users as $key => $user) {
                    $users[$key] = $user;
                    $users[$key]['thumbnail'] = $user->profile['avatar'];
                    unset($users[$key]['profile']);
                }
            }

            $response = [

                "id" => $class->id,
                "name" => $class->name,
                "description" => $class->description,
                "thumbnail" => $class->thumbnail,
                "cover" => $class->cover,
                "author_name" => $user->name,
                "author_avatar" => $avatar,
                "members" => $class->users()->count(),
                "lessons" => $lessons,
                "tags" => $class->tags,
                "users" => $users,
                "is_collaborative" => $class->is_collaborative,
                "memberOf" => !!sizeOf($isMember)

            ];

            return $this->setStatusCode(200)->respondSuccess($response);

        }

        return $this->setStatusCode(404)->respondWithError("Class Not Found");

    }


    public function update(Request $request, $id)
    {
        $class = Classes::find($id);
        $organization = Group::find($class["group_id"])->organization;
        if(isset($request['name'])){
            $class->name = $request['name'];
        }
        if(!empty($request->thumbnail)){
            $path = Files::qualityCompress($request->thumbnail, "organizations/{$organization->id}/classes/{$class->id}/icon");
            $class->thumbnail = $path;
        }
        if(!empty($request->cover)){
            $path = Files::qualityCompress($request->cover, "organizations/{$organization->id}/classes/{$class->id}/cover");
            $class->cover = $path;
        }

        if(isset($request['description'])) $class->description = $request['description'];
        var_dump($request['is_collaborative']);die;
        if(isset($request['is_collaborative'])) $class->is_collaborative = $request['is_collaborative'] === 'true' ? 1 : 0 ;
        if(isset($request['tags'])) {
            Tag::assignTag($class, $request);
        }

        $class->save();

        if (isset($request['admins']) && is_array($request['admins'])) {
            DB::table('classes_user')->where('role', 'admin')->where('classes_id', $class->id)->delete();
            foreach ($request['admins'] as $userId) {
                DB::table('classes_user')->insert(
                    ['user_id' => $userId, 'classes_id' => $class->id, 'role' => 'admin']
                );
            }
        }

        $classThumbnail = (isset($class->thumbnail)) ? $class->thumbnail : null;
        event(new ElasticClassUpdateIndex($id, $class->name, $classThumbnail));
        return Response::json($class->toArray(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $class = Classes::find($id);


        if($class["author_id"] == Auth::guard('api')->user()->id){

            $class->lessons()->detach();
            $class->delete($id);
            return $this->setStatusCode(200)->respondSuccess("success");

        }

        return $this->setStatusCode(409)->respondWithError("Lesson is not created by this user");

    }

    /**
     * User join to class
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function join(JoinClassRequest $request)
    {

        $user = Auth::guard('api')->user();

        $class = Classes::find($request->classes_id);
        $groups = Auth::guard('api')->user()->groups()->whereId($class->group_id)->first();

        #check is user already in group of this class
        if($groups){

            if(!$user->classes()->whereId($request->classes_id)->first()){
                
                $user->classes()->attach($request->classes_id);

                 return $this->setStatusCode(204)->respondSuccess([]);

            }

            return $this->setStatusCode(409)->respondWithError("Already exists conflict");

        }

        return $this->setStatusCode(409)->respondWithError("User out of class group");

    }

    public function leave($id){

        $user = Auth::guard('api')->user();

        if($user->classes()->whereId($id)->first()){

            $user->classes()->detach($id);
            return $this->setStatusCode(204)->respondSuccess([]);

        }

        return $this->setStatusCode(409)->respondWithError("Conflict");

    }

}
