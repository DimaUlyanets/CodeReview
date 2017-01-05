<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\User;
use App\Classes;
use App\Lesson;

class TopPicksController extends ApiController
{
    public function all(Request $request){
        $id = Auth::guard('api')->user()->id;

        $classes = Classes::all();
        $lessons = Lesson::all();

            $response = array();
                foreach($classes as $classKey => $class){
                    $user = User::find($class->author_id);
                    $response['classes'][$class->id]["name"] = $class->name;
                    $response['classes'][$class->id]["thumbnail"] = $class->thumbnail;
                    $response['classes'][$class->id]["id"] = $class->id;
                    $response['classes'][$class->id]["users"] = sizeOf($class->users);
                    $response['classes'][$class->id]["author"] = $user->name;

                }
                foreach($lessons as $lessonKey => $lesson){
                    $response['lessons'][$lesson->id]["name"] = $lesson->name;
                    $response['lessons'][$lesson->id]["thumbnail"] = $lesson->thumbnail;
                    $response['lessons'][$lesson->id]["id"] = $lesson->id;
                    $response['lessons'][$lesson->id]["views"] = $lesson->views;
                    $response['lessons'][$lesson->id]["author"] = $lesson->author->name;
                }

        if(!empty($response['classes']))$response['classes'] = array_values($response['classes']);
        if(!empty($response['lessons']))$response['lessons'] = array_values($response['lessons']);

        return $this->setStatusCode(200)->respondSuccess($response);
    }
}
