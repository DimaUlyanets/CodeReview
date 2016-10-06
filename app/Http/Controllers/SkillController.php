<?php

namespace App\Http\Controllers;

use App\Skill;
use Illuminate\Http\Request;

use App\Http\Requests;

class SkillController extends ApiController
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
    public function create(Request $request)
    {
        //
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

    public function suggest($tag){

        $data = Skill::where('name', 'like', "%{$tag}%")->get();

        if(isset($data[0])){

            $response = [];

            foreach($data as $key => $value){

                $response[$key]["id"] = $value->id;
                $response[$key]["name"] = $value->name;

            }

            return $this->setStatusCode(200)->respondSuccess($response);

        }else{

            return $this->setStatusCode(404)->respondWithError("Not Found");

        }

    }
}
