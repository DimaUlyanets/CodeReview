<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProfileRequest extends Request
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
            'avatar' => 'max:255',
            'icon' => 'max:255',
            'cover' => 'max:255',
            'bio' => 'max:255',
            'color' => 'max:255',
        ];
    }
}
