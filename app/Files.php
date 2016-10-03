<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Files extends Model
{
    public static function saveUserFiles($user, $request){

        $result = [];

        if(!empty($request->cover)){
            #$result["cover"] = env("APP_S3") . $request->cover->store("users/{$user->id}/cover_photo", 's3');
        }
        if(!empty($request->avatar)){
            $result["avatar"] = env("APP_S3") . $request->avatar->store("users/{$user->id}/profile_avatar", 's3');
        }

        return $result;


    }

    public static function saveEntityIcon($entity, $request, $type, $organization){

        #Storage::disk('s3')->put("organizations/{$user->id}/profile/kitty.jpg", file_get_contents("https://yomotherboard.com/wp-content/uploads/2015/02/laravel.png"));


    }

}
