<?php

namespace App\Http\Controllers;

use App\Classes;
use App\Http\Requests\CreateLessonRequest;
use App\Lesson;
use App\Skill;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class LessonController extends ApiController
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
    public function create(CreateLessonRequest $request)
    {

        $data = $request->except(['api_token']);
        $user = Auth::guard('api')->user();

        $data["author_id"] = $user->id;

        if(!$request->group_id){

            $data["group_id"] = $user->organizations()->whereDefault(1)->first()->group()->whereDefault(1)->first()->id;

        }

        $lesson = Lesson::create($data);

        if($request->class_id){

            $class = Classes::find($request->class_id);
            $class->lessons()->attach($lesson->id);

        }

        if($request->skills){

            foreach($request->skills as $value){
                
                $skill = Skill::whereName($value)->first();
                if(!$skill){
                    $skill = Skill::create(["name" => $value]);
                }
                $lesson->skills()->attach($skill->id);

            }

        }

        return $this->setStatusCode(200)->respondSuccess($lesson);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lesson = Lesson::find($id);

        if($lesson){



            $response = [

                "id" => $lesson->id,
                "name" => $lesson->name,
                "description" => $lesson->description,
                "thumbnail" => $lesson->thumbnail,
                "lesson_file" => $lesson->lesson_file,
                "difficulty" => $lesson->difficulty,
                "views" => ++$lesson->views,

            ];

            $lesson->views = $lesson->views;
            $lesson->save();

            return $this->setStatusCode(200)->respondSuccess($response);

        }

        return $this->setStatusCode(404)->respondWithError("Lesson Not Found");

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

        $data = Lesson::where('name', 'like', "{$tag}%")->get();

        if(isset($data[0])){

            $response = [];

            foreach($data as $key => $value){

                $response[$key]["id"] = $value->id;
                $response[$key]["name"] = $value->name;

            }

            return $this->setStatusCode(200)->respondSuccess($response);

        }

    }
}
