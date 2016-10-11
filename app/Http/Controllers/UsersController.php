<?php

namespace App\Http\Controllers;

use App\Files;
use App\Group;
use App\Http\Requests\CreateProfileRequest;
use App\Http\Requests\UserCreateRequest;
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

            $user->organizations()->attach($default->id);
            $user->groups()->attach($default->group->first()->id);

            return Response::json($user->toArray(), 200);

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
        $user = User::find($id);

        if(!$user){

            return $this->setStatusCode(404)->respondWithError("User does not exists");

        }

        return $this->respond($user);



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

    public function groups($id = null){

        $id = $id ? $id : Auth::guard('api')->user()->id;

        $user = User::find($id);

        if($user){

            if($id != Auth::guard('api')->user()->id){

                return $this->setStatusCode(403)->respondWithError("Forbidden");

            }

            $response = array();
            foreach($user->groups as $key => $group){

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

                if(isset($files["cover"]))$data["cover"] = $files["cover"];
                if(isset($files["avatar"]))$data["avatar"] = $files["avatar"];

                $resutl = Profile::create($data);
                return $this->setStatusCode(200)->respondSuccess($resutl);

            }

            return $this->setStatusCode(409)->respondWithError("User already created profile");

        }

        return $this->setStatusCode(404)->respondWithError("User does not exists");

    }

    public function classes($id){

        $user = User::find($id);

        if($user){

            if($id != Auth::guard('api')->user()->id){

                return $this->setStatusCode(403)->respondWithError("Forbidden");

            }

            $response = [];

            foreach($user->classes as $value){

                $response[$value->id]["id"] = $value->id;
                $response[$value->id]["name"] = $value->name;
                $response[$value->id]["description"] = $value->description;

            }

            return $this->setStatusCode(200)->respondSuccess($response);


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

}
