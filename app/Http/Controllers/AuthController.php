<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\EmailValidateRequest;
use App\Http\Requests\LoginRequest;
use App\Organization;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate(LoginRequest $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();
            $user->api_token = str_random(60);
            $user->save();

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

    public function logout(){

        $user = Auth::guard('api')->user();
        $user->api_token = NULL;
        $user->save();
        return Response::json(["success"], 200);

    }

    public function validateEmail(EmailValidateRequest $request){

        return Response::json(["success"], 200);

    }

    public function test(){


        #Storage::disk('s3')->exists('file.jpg');

        Storage::disk('s3')->put('testing/trampampam/tsetet/kitty.jpg', file_get_contents("https://yomotherboard.com/wp-content/uploads/2015/02/laravel.png", 'public'));
        #$contents = Storage::disk('s3')->url('public.jpg');
        #$contents = Storage::disk('s3')->get('public.jpg');
        dd(Storage::disk('s3')->url("testing/trampampam/tsetet/kitty.jpg"));

    }

}
