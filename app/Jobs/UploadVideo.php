<?php

namespace App\Jobs;

use App\Lesson;
use FFMpeg\FFMpeg;
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

        FFMpeg::fromDisk('videos')
            ->open('steve_howe.mp4')
            ->addFilter(function ($filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension(640, 480));
            })
            ->export()
            ->toDisk('converted_videos')
            ->inFormat(new \FFMpeg\Format\Video\X264)
            ->save('small_steve.mkv');


        #\Pbmedia\LaravelFFMpeg\FFMpeg::

        Storage::disk('s3')->put($this->local, file_get_contents(storage_path('app/public').DIRECTORY_SEPARATOR .$this->local), 'public');
        $this->lesson->lesson_file = env("APP_S3") . $this->local;
        $this->lesson->save();
        unlink(storage_path('app/public').DIRECTORY_SEPARATOR .$this->local);

    }
}
