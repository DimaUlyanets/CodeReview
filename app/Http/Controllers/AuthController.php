<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate(Request $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            return response()->json(
                array(
                    "api_token" => Auth::user()->api_token,
                    "name" => Auth::user()->name,
                    "username" => Auth::user()->username,
                )
            );

        }

        return response()->json(array(
            'error' => [
                'message' => "Unauthorized",
                'status_code' => 401
            ]), 401);
    }

}
