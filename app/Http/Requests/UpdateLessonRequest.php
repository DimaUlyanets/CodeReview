<?php

namespace App\Http\Requests;

use App\Classes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateLessonRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name' => 'sometimes|required|max:255',
            'description' => 'sometimes|required|max:512',
            'thumbnail' => 'image|max:10240',
            'lesson_file' => 'sometimes|required|file|mimes:mp4,pdf',
            'difficulty' => 'sometimes|required|between:0,100|numeric',
            'type' => 'sometimes|numeric|required',
            'group_id' => 'numeric|exists:groups,id',
            'class_id' => 'numeric|exists:classes,id',
            'skills' => 'max:255',
            'tags' => 'max:255',
            'organization_id' => 'sometimes|exists:organizations,id|numeric|required'
        ];
    }
}
