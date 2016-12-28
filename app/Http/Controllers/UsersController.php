<?php

namespace App\Http\Controllers;

use App\Classes;
use App\Files;
use App\Group;
use App\Http\Requests\CreateProfileRequest;
use App\Http\Requests\UserCreateRequest;
use App\Events\ElasticUserAddToIndex;
use App\Lesson;
use App\Organization;
use App\Privacy;
use App\Profile;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class UsersController extends ApiController
{

    const LESSONS_LIMITS = 5;
    const CLASSES_LIMITS = 5;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return Response::json([
            User::all(),
            200
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(UserCreateRequest $request)
    {

        $user = User::create([
            'name' => "{$request->first_name} {$request->last_name}",
            'username' => $request->username,
            'email' => $request->email,
            'api_token' => str_random(60),
            'password' => Hash::make($request->password),
        ]);

        if($user){

            #Get default organization and attach to created user
            $default = Organization::whereDefault(1)->first();

            $user->organizations()->attach([$default->id => ['role' => 'member']]);
            $user->groups()->attach([$default->group->first()->id => ['role' => 'member']]);

            return Response::json($user->toArray(), 200);

        }

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        $id = $id ? $id : Auth::guard('api')->user()->id;
        $user = User::with('organizations', 'profile')->find($id);

        if(!$user){

            return $this->setStatusCode(404)->respondWithError("User does not exists");

        }

        return $this->respond($user);

    }

    public function getUserOrganization($id){

        $user = Auth::guard('api')->user();
        $response = [];

        $organization = $user->organizations()->whereId($id)->first();
        $response["organization"] = $organization;
        $groups = $user->groups()->whereOrganizationId($organization->id)->get();
        $response["classes"] = [];
        foreach($groups as $key => $group){
           $response["classes"] = array_merge($response["classes"], $user->classes()->whereGroupId($group->id)->get()->toArray());
        }

        usort($response["classes"], function($a, $b) {
              return $a['id']- $b['id'];
        });

        $groupsNotDeFault = $user->groups()->whereOrganizationId($organization->id)->whereDefault(0)->get();//TODO refactor
        $response["groups"] = $groupsNotDeFault->toArray();
        $response["organization"]["tags"] = $organization->tags->toArray();
        $response["organization"]["admins"] = $organization->admins->toArray();
        return Response::json($response, 200);
    }

    public function getProfile($id = null){
        $private = $id ? true : false;
        $id = $id ? $id : Auth::guard('api')->user()->id;
        $user = User::with('groups', 'classes', 'lessons')->find($id);
        $response = [];
        if ($user) {
            $profile = $user->profile;
            $response["id"] = $user->id;
            $response["name"] = $user->name;
            $response["thumbnail"] = $profile ? $profile->avatar : null;
            $response["cover"] = $profile ? $profile->cover : null;
            $response["following"] = !!$user->followers()->where('follower_id', Auth::guard('api')->user()->id)->first();
            $response["groups"] = [];
            $visibleGroupIds = [];
            $allGroups = $user->groups()->whereDefault(0)->get();
            foreach($allGroups as $key => $group) {
                if (!Group::userHasAccess($group)) continue;
                array_push($response["groups"], $group);
                array_push($visibleGroupIds, $group->id);
            }

            $response["classes"] = [];
            $allClasses = $user->classes()->get();
            foreach($allClasses as $key => $class) {
                if (!Group::userHasAccess($class->group)) continue;
                array_push($response["classes"], $class);
            }

            $response["lessons"] = [];
            $allLessons = $user->lessons()->get();
            foreach($allLessons as $key => $lesson) {
                if (!Group::userHasAccess($lesson->group)) continue;
                array_push($response["lessons"], $lesson);
            }

            return Response::json($response, 200);
        } else {
             return $this->setStatusCode(404)->respondWithError("User not found");
        }
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

    public function groups(Request $request, $id = null){

        $id = $id ? $id : Auth::guard('api')->user()->id;
        $user = User::find($id);

        if($user){

            if($id != Auth::guard('api')->user()->id){

                return $this->setStatusCode(403)->respondWithError("Forbidden");

            }
            $groups = $request->input('admin') ? $user->controlledGroups : $user->groups;
            $response = array();
            foreach($groups as $key => $group){
                if (!$group->default) {
                    $response[$group->id]["name"] = $group->name;
                    $response[$group->id]["thumbnail"] = $group->icon;
                    $response[$group->id]["id"] = $group->id;
                }

            }

            return $this->setStatusCode(200)->respondSuccess(array_values($response));

        }

        return $this->setStatusCode(404)->respondWithError("User Not Found");

    }

    public function profile(CreateProfileRequest $request, $id){

        $user = User::find($id);
        $data = (array)$request->all();
        unset($data["api_token"]);

        if(empty($data)){

            return $this->setStatusCode(204)->respondSuccess(["No content"]);

        }

        if($user){

            $data["user_id"] = $id;

            if(!$user->profile){

                $files = Files::saveUserFiles($user, $request);

                $data["cover"] = isset($files["cover"]) ? $files["cover"] : 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/cover.png';
                $data["avatar"] = isset($files["avatar"]) ? $files["avatar"] : 'https://s3-eu-west-1.amazonaws.com/bck-lessons/default/user.svg';

                $result = Profile::create($data);
                $usrThumbnail = $result->thumbnail || null;
                event(new ElasticUserAddToIndex($user->id, $user->name, $usrThumbnail));
                return $this->setStatusCode(200)->respondSuccess($result);

            }

            return $this->setStatusCode(409)->respondWithError("User already created profile");

        }

        return $this->setStatusCode(404)->respondWithError("User does not exists");

    }

    public function classes($id = null, $skip = 0){

        $id = $id ? $id : Auth::guard('api')->user()->id;
        $user = User::find($id);

        if($user){

            if($id != Auth::guard('api')->user()->id){

                return $this->setStatusCode(403)->respondWithError("Forbidden");

            }

            $response = [];
            $classes = Classes::with('group', 'group.organization', 'lessons', 'users')->whereAuthorId($id)->skip($skip)->take(self::CLASSES_LIMITS)->get();


            foreach($classes as $key => $value){

                $response[$value->id]["id"] = $value->id;
                $response[$value->id]["name"] = $value->name;
                $response[$value->id]["organization"]["id"] = $value->group->organization->id;
                $response[$value->id]["organization"]["icon"] = $value->group->organization->icon;
                $response[$value->id]["lessons_num"] = count($value->lessons);
                $response[$value->id]["users_num"] = count($value->users);
                $response[$value->id]["thumbnail"] = $value->thumbnail;
                $response[$value->id]["description"] = $value->description;
                $response[$value->id]["group_id"] = $value->group_id;
                $response[$value->id]["group_icon"] = $value->group->icon;


            }

            return $this->setStatusCode(200)->respondSuccess(array_values($response));


        }

        return $this->setStatusCode(404)->respondWithError("User does not exists");

    }

    public function lessons($id = null, $skip = 0){

        $id = $id ? $id : Auth::guard('api')->user()->id;
        $user = User::find($id);

        if($user){

            if($id != Auth::guard('api')->user()->id){

                return $this->setStatusCode(403)->respondWithError("Forbidden");

            }

            $response = [];

            $lessons = Lesson::with('group', 'group.organization')->whereAuthorId($id)->skip($skip)->take(self::LESSONS_LIMITS)->get();

            foreach($lessons as $key => $value){

                $response[$key]["id"] = $value->id;
                $response[$key]["name"] = $value->name;
                $response[$key]["views"] = $value->views;
                $response[$key]["group"]["id"] = $value->group->id;
                $response[$key]["group"]["icon"] = $value->group->icon;
                $response[$key]["organization"]["id"] = $value->group->organization->id;
                $response[$key]["organization"]["icon"] = $value->group->organization->icon;

            }

            return $this->setStatusCode(200)->respondSuccess(array_values($response));

        }

        return $this->setStatusCode(404)->respondWithError("User does not exists");

    }

    public function suggest(Request $request){

        $user = User::whereUsername($request->username)->first();

        if($user){

            $suggestion = [];

            $list = User::all();
            $existing = [];

            foreach($list as $value){

                $existing[] = $value->username;

            }

            $year = date('Y');

            if(!in_array("{$request->last_name}_{$request->first_name}", $existing))$suggestion[] = "{$request->last_name}_{$request->first_name}";
            if(!in_array("{$request->first_name}_{$request->last_name}", $existing))$suggestion[] = "{$request->first_name}_{$request->last_name}";
            if(!in_array("{$request->last_name}_{$request->first_name}_{$year}", $existing))$suggestion[] = "{$request->last_name}_{$request->first_name}_{$year}";
            if(!in_array("{$year}_{$request->last_name}_{$request->first_name}", $existing))$suggestion[] = "{$year}_{$request->last_name}_{$request->first_name}";
            if(!in_array("{$year}_{$request->username}", $existing))$suggestion[] = "{$year}_{$request->username}";
            if(!in_array("{$request->username}_{$year}", $existing))$suggestion[] = "{$request->username}_{$year}";

            #put any words to this array to combine with username
            $listOfWords = [""];

            foreach($listOfWords as $value){

                if(!in_array("{$value}_{$request->username}", $existing))$suggestion[] = "{$value}_{$request->username}";

            }

            return $this->setStatusCode(409)->respondSuccess($suggestion);

        }

        return $this->setStatusCode(204)->respondSuccess(["No content"]);

    }

    public function follow($id)
    {
       $user = User::find($id);

       if ($user) {
           $follower = Auth::guard('api')->user();
           User::followUser($follower, $user);
           $user['followers'] = $user->followers;
           $user['following'] = true;
           return $this->setStatusCode(200)->respondSuccess($user);
       }

       return $this->setStatusCode(404)->respondWithError("User Not Found");
    }

     public function unFollow($id)
     {
          $user = User::find($id);

          if ($user) {
              $follower = Auth::guard('api')->user();
              User::unFollowUser($follower, $user);
              $user['followers'] = $user->followers;
              $user['following'] = false;
              return $this->setStatusCode(200)->respondSuccess($user);
          }

          return $this->setStatusCode(404)->respondWithError("User Not Found");
     }

    public function filter($name){

        $response = User::where('name', 'like', "%{$name}%")->get(['name', 'id']);
        return $this->setStatusCode(200)->respondSuccess($response);

    }

}
