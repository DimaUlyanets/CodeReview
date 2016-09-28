<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassCreateRequest extends Request
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
            'name' => 'required|max:255|alpha_num|unique:classes,name,NULL,id,group_id,' . $this->group_id,
            'description' => 'required|max:140',
            'thumbnail' => 'required',
            'group_id' => 'exists:groups,id|required|numeric',
            'is_collaborative' => 'numeric|required|numeric',
        ];
    }
}
