<?php

namespace App\Http\Controllers;

use App\Files;
use App\Http\Requests\OrganizationCreateRequest;
use App\Organization;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
        $result->icon = Files::qualityCompress($request->icon, "organizations/{$result->id}/icon");
        $result->save();

        if($result){

            if($request->tags){

                Tag::assignTag($result, $request);

            }

            Organization::createDefaultGroup($result);
            return Response::json($result->toArray(), 200);

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

        $organization = Organization::find($id);
        $test = [];
        $test['id']= $organization->id;
        $test['name']= $organization->name;
        $test['description']= $organization->description;
        $test['thumbnail']= $organization->thumbnail;
        $test['cover']= $organization->cover;

        $organizationGroups = $organization->group->toArray();
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



        $response['organization']=$test;
        $response['lessons']= $lessons;
        $response['classes']= $classes;
        $response['groups']= $organizationGroups;

        return Response::json($response, 200);
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
}
