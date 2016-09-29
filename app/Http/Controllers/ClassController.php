<?php

namespace App\Http\Controllers;

use App\Classes;
use App\Http\Requests\ClassCreateRequest;
use App\Http\Requests\JoinClassRequest;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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
        $data["group_id"] = $request->group_id;

        $class = Classes::create($data);

        if($class){

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
        //
    }


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

    /**
     * User join to class
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function join(JoinClassRequest $request)
    {

        $user = User::find($request->user_id);

        if(!$user->classes()->whereId($request->classes_id)->first()){

            $user->classes()->attach($request->classes_id);

            return $this->setStatusCode(200)->respondSuccess(
                array(
                    "user_id" => $request->user_id,
                    "class_id" => $request->classes_id
                )
            );

        }

        return $this->setStatusCode(409)->respondWithError("Already exists conflict");

    }
}
