<?php

namespace App\Jobs;

use App\Lesson;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;


class UploadVideo implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $local;
    public $lesson;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($local, Lesson $lesson)
    {
        $this->local = $local;
        $this->lesson = $lesson;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        #convert  video here
        #https://github.com/PHP-FFMpeg/PHP-FFMpeg
        #local vidoe stored here: storage_path('app/public').DIRECTORY_SEPARATOR .$this->local

        $localfile = $this->local;
        $resized = str_replace('.mp4', 'resized.mp4', $this->local);

        $file = storage_path('app/public').DIRECTORY_SEPARATOR . $localfile;
        $file2 = storage_path('app/public').DIRECTORY_SEPARATOR . $resized;

        exec('ffmpeg -i '. $file . ' -b:v ' .env('VIDEO_BITRATE'). 'k -vcodec libx264 -acodec libmp3lame '. $file2);

        Storage::disk('s3')->put($resized, file_get_contents($file2), 'public');
        $this->lesson->lesson_file = env("APP_S3") . $resized;
        $this->lesson->save();
        unlink($file);
        unlink($file2);

    }
}
