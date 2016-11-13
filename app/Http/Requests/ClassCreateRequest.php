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

        #check is user in group where he want to create class
        if(!$this->group_id)return true;
        if(Auth::guard('api')->user()->groups()->whereId($this->group_id)->first())return true;
        return false;

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
            $this->group_id = Auth::guard('api')->user()->organizations()->whereId($this->organization_id)->first()->group()->whereDefault(1)->first()->id;
        }

        return [
            'name' => 'required|max:255|unique:classes,name,NULL,id,group_id,' . $this->group_id,
            'description' => 'required|max:140',
            'thumbnail' => 'image|max:10240',
            'group_id' => 'exists:groups,id|numeric',
            'is_collaborative' => 'numeric|required|boolean',
            'tags' => 'max:255',
            'organization_id' => 'exists:organizations,id|numeric|required'
        ];
    }
}
