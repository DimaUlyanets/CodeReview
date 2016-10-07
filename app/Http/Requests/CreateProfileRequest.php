<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateProfileRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        if($this->id == Auth::guard('api')->user()->id)return true;
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
            'avatar' => 'image',
            'cover' => 'image',
            'bio' => 'max:255',
            'color' => 'max:255',
        ];
    }
}
