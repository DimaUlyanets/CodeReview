<?php

namespace App;

use App\Jobs\UploadVideo;
use FFMpeg\FFMpeg;
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

        $extension = $resource->extension();


        $local = $resource->store($path, 'public');
        $img = Image::make(storage_path('app/public').DIRECTORY_SEPARATOR .$local);

        $img->save(storage_path('app/public').DIRECTORY_SEPARATOR .$local, env('COMPRESS_RATIO'));

        Storage::disk('s3')->put($local, file_get_contents(storage_path('app/public').DIRECTORY_SEPARATOR .$local), 'public');
        unlink(storage_path('app/public').DIRECTORY_SEPARATOR .$local);

        return env("APP_S3") . $local;

    }

    public static function uploadLessonFile($resource, $path, $lesson){

        $extension = $resource->extension();

        if($extension == "pdf"){

            $lesson->lesson_file = env("APP_S3") . $resource->store($path, 's3');
            $lesson->save();

            return $lesson->lesson_file ;

        }

        $lesson->lesson_file = "processing";
        $lesson->save();

        $local = $resource->store($path, 'public');
        dispatch(new UploadVideo($local, $lesson));

        return "processing";

    }


}
