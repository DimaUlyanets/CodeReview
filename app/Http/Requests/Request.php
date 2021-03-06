<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

abstract class Request extends FormRequest {

    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function response(array $errors)
    {
        return new JsonResponse(array("errors" => $errors), 422);
    }

    public function forbiddenResponse()
    {

        return new Response([
            'error' => [
                'message' => "Forbidden",
                'status_code' => 403
            ]
        ], 403);
    }

}
