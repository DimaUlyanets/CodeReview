<?php

namespace App\Http\Controllers;

use App\Classes;
use App\ElasticSearch\ClassSearch;
use App\Http\Requests\ClassCreateRequest;
use App\Http\Requests\JoinClassRequest;
use App\Tag;
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(ClassCreateRequest $request)
    {

        $data = (array)$request->all();
        $data["group_id"] = $request->group_id;
        $data["author_id"] = Auth::guard('api')->user()->id;

        $class = Classes::create($data);

        if ($class) {

            if ($request->tags) {

                foreach ($request->tags as $value) {

                    $tag = Tag::whereName($value)->first();
                    if (!$tag) {
                        $tag = Tag::create(["name" => $value]);
                    }
                    $class->tags()->attach($tag->id);

                }

            }


            //START BUILD  DATA TO SEARCH
            $idClassToSearch = $class->id;
            $nameClassToSearch = $data['name'];
            $thumbnailClassToSearch = (isset($data['thumbnail'])) ? $data['thumbnail'] : null;

            $search = new ClassSearch();
            $search->addToIndex($idClassToSearch, $nameClassToSearch, $thumbnailClassToSearch);

            return Response::json($class->toArray(), 200);

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $class = Classes::find($id);

        if ($class) {

            $user = User::find($class->author_id);

            $lessons = [];
            foreach ($class->lessons as $key => $value) {

                $lessons[$key]["name"] = $value->name;
                $lessons[$key]["thumbnail"] = $value->thumbnail;
                $lessons[$key]["author_name"] = $value->author->name;

            }

            $avatar = ($user->profile) ? $user->profile->avatar : "";

            $response = [

                "name" => $class->name,
                "description" => $class->description,
                "author_name" => $user->name,
                "author_avatar" => $avatar,
                "members" => $class->users()->count(),
                "lessons" => $lessons

            ];

            return $this->setStatusCode(200)->respondSuccess($response);

        }

        return $this->setStatusCode(404)->respondWithError("Class Not Found");

    }


    public function update(Request $request, $id)
    {
//        Need complete method and pass (new name and new thumbnail)!!!

//        $search = new ClassSearch();
//        $search->updateIndex($id,$name,$thumbnail);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //Need complete method and pass (id)!!!
        $search = new ClassSearch();
        $search->deleteIndex($id);
    }

    /**
     * User join to class
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function join(JoinClassRequest $request)
    {

        $user = Auth::guard('api')->user();

        $class = Classes::find($request->classes_id);
        $groups = Auth::guard('api')->user()->groups()->whereId($class->group_id)->first();

        #check is user already in group of this class
        if ($groups) {

            if (!$user->classes()->whereId($request->classes_id)->first()) {

                $user->classes()->attach($request->classes_id);

                return $this->setStatusCode(200)->respondSuccess(
                    array(
                        "user_id" => $user->id,
                        "class_id" => $request->classes_id
                    )
                );

            }

            return $this->setStatusCode(409)->respondWithError("Already exists conflict");

        }

        return $this->setStatusCode(409)->respondWithError("User out of class group");

    }

    public function leave($id)
    {

        $user = Auth::guard('api')->user();

        if ($user->classes()->whereId($id)->first()) {

            $user->classes()->detach($id);
            exit;

        }

        return $this->setStatusCode(409)->respondWithError("Conflict");

    }

}
