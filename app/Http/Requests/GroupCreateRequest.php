<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class GroupCreateRequest extends Request
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

        if(!$this->organization_id){
            $this->organization_id = Auth::guard('api')->user()->organizations()->whereDefault(1)->first()->id;
        }

        return [
            'name' => 'required|max:255|unique:groups,name,NULL,id,organization_id,' . $this->organization_id,
            'description' => 'required|max:140',
            'organization_id' => 'numeric|exists:organizations,id',
            'privacy_id' => 'numeric|required|exists:privacy,id',
            'tags' => 'array',
        ];
    }
}
