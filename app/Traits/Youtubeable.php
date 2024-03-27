<?php

namespace App\Traits;

use App\Models\Channel;
use App\Models\ChannelVideo;
use App\services\Google\Profile;
use App\services\Google\Youtube;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait Youtubeable
{
    protected Youtube $youtube;

    protected function getYoutubeInstance(): void
    {
        $this->youtube = app(Youtube::class);

        if ($service = $this->service()) {
            $this->youtube->setAccessToken($token = $service->ensureToken());

        }
    }

    public function search(array $options = ['q' => '', 'channelId' => ''])
    {
        return $this->youtube
            ->search($options, true);
    }

    public function channel(array $options = ['id' => '', 'categoryId' => ''])
    {
        return $this->youtube
            ->searchChannels($options, true);
    }

    public function playlists(array $options = ['id' => '', 'channelId' => ''])
    {
        return $this->youtube
            ->searchChannels($options, true);
    }

    /**
     * Store channels lists
     */
    public function storeChannels(string $channels): Collection
    {
        $this->getYoutubeInstance();
        $channels = $this->channel([
            'id' => $channels,
        ]);

        return collect($channels)
            ->map(function ($c) {
                return $this->channels()
                    ->updateOrCreate([
                        'uuid' => $c->id,
                        'user_id' => $this->id,
                    ], [
                        'title' => $c->snippet->title,
                        'etag' => $c->etag,
                        'kind' => $c->kind,
                        'country' => $c->snippet->country,
                        'custom_url' => $c->snippet->customUrl,
                        'url' => 'https://www.youtube.com/channel/'.$c->id,
                        'language' => $c->snippet->defaultLanguage,
                        'description' => $c->snippet->description,
                        'published_at' => $c->snippet->publishedAt,
                        'thumbnail' => $c->snippet->thumbnails,
                    ]);
            });

    }

    public function storeSearch(array $options = [
        'q' => '',
        'type' => 'video',
    ], bool $toJon = true)
    {

        $this->getYoutubeInstance();
        $results = $this->youtube
            ->search($options, $toJon);

        return collect($results)
            ->map(function ($c) {
                $channel = $this->channels()
                    ->updateOrCreate([
                        'uuid' => $c->snippet->channelId,
                        'user_id' => $this->id,
                    ], [
                        'title' => $c->snippet->channelTitle,
                        'url' => 'https://www.youtube.com/channel/'.$c->snippet->channelId,
                    ]);
                $this->storeVideo($channel, $c);

                return $channel->fresh()->load('videos');
            });
    }

    public function storeVideo(Channel $channel, mixed $video): Model
    {
        return $this->videos()
            ->updateOrCreate([
                'uuid' => $video->id->videoId,
            ], [
                'etag' => $video->etag,
                'kind' => $video->id->kind,
                'playlist_id' => $video->id->playlistId,
                'description' => $video->snippet->description,
                'published_at' => $video->snippet->publishedAt,
                'title' => $video->snippet->title,
                'thumbnail' => $video->snippet->thumbnails,
                'live_broadcast' => $video->snippet->liveBroadcastContent,
                'url' => 'https://www.youtube.com/watch?v='.$video->id->videoId,
                'channel_id' => $channel->id,
            ]);
    }

    public function upload(ChannelVideo $video)
    {
        $this->getYoutubeInstance();
        $this->youtube->upload($video->filename, [
            'title' => $video->title,
            'description' => $video->description,
            'tags' => $video->tag,
            'category_id' => $video?->category,
        ]);
    }

    public function owesChannel(): Model|Channel|null
    {
        return $this->channels()
            ->where('is_owner', true)
            ->first();
    }

    public function id()
    {
        return $this->profile->id();
    }

    public function getClientId(mixed $token)
    {
        $profile = app(Profile::class);
        $profile->setAccessToken($token);

        return $profile->id();
    }

    public function getClient(mixed $token)
    {
        $profile = app(Profile::class);
        $profile->setAccessToken($token);

        return $profile;
    }
}
