<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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

        if(!$this->group_id){
            $this->group_id = Auth::guard('api')->user()->organizations()->whereDefault(1)->first()->group()->whereDefault(1)->first()->id;
        }

        return [
            'name' => 'required|max:255|alpha_num|unique:classes,name,NULL,id,group_id,' . $this->group_id,
            'description' => 'required|max:140',
            'thumbnail' => 'required',
            'group_id' => 'exists:groups,id|numeric',
            'is_collaborative' => 'numeric|required|numeric',
        ];
    }
}
