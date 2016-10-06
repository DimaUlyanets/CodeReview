<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Files extends Model
{
    public static function saveUserFiles($user, $request){

        $result = [];

        if(!empty($request->cover)){

            $result["cover"] = self::qualityCompress($request->cover, "users/{$user->id}/cover_photo");

        }
        if(!empty($request->avatar)){

            $result["avatar"] = self::qualityCompress($request->avatart, "users/{$user->id}/profile_avatar");

        }

        return $result;


    }

    public static function qualityCompress($resource, $path){

        $local = $resource->store($path, 'public');
        $img = Image::make(storage_path('app/public').DIRECTORY_SEPARATOR .$local);
        $img->save(storage_path('app/public').DIRECTORY_SEPARATOR .$local, env('COMPRESS_RATIO'));
        Storage::disk('s3')->put($local, file_get_contents(storage_path('app/public').DIRECTORY_SEPARATOR .$local), 'public');

        return env("APP_S3") . $local;

    }


}
