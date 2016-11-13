<?php

namespace App\Http\Requests;

use App\Classes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateLessonRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {


        $user = Auth::guard('api')->user();

        $group_id = $this->group_id;

        if(!$this->group_id){

            $group_id = $user->organizations()->whereDefault(1)->first()->group()->whereDefault(1)->first()->id;

        }

        if($this->class_id){

            $class = Classes::find($this->class_id);

            if(!$class)return false;

            #is provided class under provided group
            if(!$class->group()->whereId($group_id)->first())return false;

            #is user under provided group
            if(!$user->groups()->whereId($class->group->id)->first())return false;


        }

        if(!$user->groups()->whereId($group_id)->first()){

            return false;

        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        if(!$this->organization_id){
            return [
                'organization_id' => 'exists:organizations,id|numeric|required'
            ];
        }

        if(!$this->group_id){

            $organization =  Auth::guard('api')->user()->organizations()->whereId($this->organization_id)->first();
            if($organization){
                $this->group_id = $organization->group()->whereDefault(1)->first()->id;
            }

        }


        return [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'thumbnail' => 'image|max:10240',
            #'lesson_file' => 'required|file|mimes:mp4,pdf',
            'difficulty' => 'required|between:0,100|numeric',
            'type' => 'numeric|required',
            'group_id' => 'numeric|exists:groups,id',
            'class_id' => 'numeric|exists:classes,id',
            'skills' => 'max:255',
            'tags' => 'max:255',
            'organization_id' => 'exists:organizations,id|numeric|required'
        ];
    }
}
