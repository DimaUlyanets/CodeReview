<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProfileRequest;
use App\Http\Requests\UserCreateRequest;
use App\Organization;
use App\Profile;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
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

            return $this->respondNotFound('User does not exists', 404);

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

    public function groups($id){

        $user = User::find($id);
        if($user){

            $response = array();

            foreach($user->groups as $key => $group){

                $response[$key]["name"] = $group->name;
                $response[$key]["icon"] = $group->icon;
                $response[$key]["id"] = $group->id;

            }

            return $this->setStatusCode(200)->respondSuccess($response);

        }

        return $this->setStatusCode(404)->respondWithError("User Not Found");

    }

    public function profile(CreateProfileRequest $request, $id){

        $user = User::find($id);
        $request->replace(array('user_id' => $id));
        $data = (array)$request->all();
        unset($data["api_token"]);

        if($user){

            $resutl = Profile::create($data);
            return $this->setStatusCode(200)->respondSuccess($resutl);

        }

        return $this->setStatusCode(404)->respondWithError("User does not exists");

    }

}
