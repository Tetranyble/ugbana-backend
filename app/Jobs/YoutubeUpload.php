<?php

namespace App\Jobs;

use App\Models\ChannelVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class YoutubeUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 14400;

    /**
     * Create a new job instance.
     */
    public function __construct(protected ChannelVideo $video)
    {
        $this->onQueue('downloads');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (isset($this->video->filename)) {
            $this->video->user->upload($this->video);
            if (File::exists($this->video->filename)) {
                File::delete($this->video->filename);
                $this->video->update([
                    'filename' => null,
                    'published_at' => now(),
                ]);
            }
        }

    }
}
