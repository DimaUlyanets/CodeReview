<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLessonRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'max:255',
            'thumbnail' => 'max:255',
            'lesson_file' => 'required|max:255',
            'difficulty' => 'required|between:0,100',
            'group_id' => 'required|max:255',
        ];
    }
}
