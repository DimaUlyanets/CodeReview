<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests;

class TagController extends ApiController
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
       $tag = Tag::find($id);

       if ($tag) {
           $response = Tag::getTagInfo($tag);

           return $this->setStatusCode(200)->respondSuccess($response);
       }

        return $this->setStatusCode(404)->respondWithError("Topic Not Found");
    }

    /**
     * Adds user follow relation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function follow($id)
    {
       $tag = Tag::find($id);

       if ($tag) {
           $user = Auth::guard('api')->user();
           Tag::followTag($user, $tag);
           $tag['users'] = $tag->users;
           $tag['following'] = true;
           return $this->setStatusCode(200)->respondSuccess($tag);
       }

       return $this->setStatusCode(404)->respondWithError("Topic Not Found");
    }

    /**
     * Removes user follow relation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unFollow($id)
    {
       $tag = Tag::find($id);

       if ($tag) {
           $user = Auth::guard('api')->user();
           Tag::unFollowTag($user, $tag);
           $tag['users'] = $tag->users;
           $tag['following'] = false;
           return $this->setStatusCode(200)->respondSuccess($tag);
       }

       return $this->setStatusCode(404)->respondWithError("Topic Not Found");
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

        $data = Tag::where('name', 'like', "%{$tag}%")->get();

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
