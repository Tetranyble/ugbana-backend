<?php

namespace App\Jobs;

use App\Models\ChannelVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class DownloadYoutube implements ShouldQueue
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
        $yt = new YoutubeDl();

        $collection = $yt->setBinPath(config('pensuh.binary.ytdlp'))->download(
            Options::create()
                ->geoByPass()
                ->output('%(id)s.%(ext)s')
                ->downloadPath(storage_path('app/public/video/downloads'))
                ->url($this->video->url)
        );
        $this->record($collection);
        YoutubeUpload::dispatch($this->video);
    }

    public function record($collection)
    {
        foreach ($collection->getVideos() as $video) {
            if ($video->getError() !== null) {
                Log::error("Error downloading video: {$video->getError()}.");
            } else {
                $this->video
                    ->update([
                        'tag' => $video->getTags(),
                        'repost_count' => $video->getRepostCount(),
                        'resolution' => $video->getResolution(),
                        'playlist' => $video->getPlaylist(),
                        'playlist_id' => $video->getPlaylistId(),
                        'playlist_index' => $video->getPlaylistIndex(),
                        'view_count' => $video->getViewCount(),
                        'duration' => $video->getDuration(),
                        'filename' => $video->getFilename(),
                        'artist' => $video->getArtist(),
                    ]);
            }
        }
    }
}
