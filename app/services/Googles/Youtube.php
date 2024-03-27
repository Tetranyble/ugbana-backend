<?php

namespace App\services\Googles;

use App\Enums\StorageProvider;
use App\Models\Channel;
use App\Models\WebService;
use Google\Client;
use Google\Service\Exception;

class Youtube
{
    protected Client $client;

    /** @var \Google_Service_YouTube */
    protected $youtube;

    private $videoId;

    private $thumbnailUrl;

    /**
     * Constructor accepts the Google Client object, whilst setting the configuration options.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->client->setApplicationName(config('youtube.application_name'));

        $this->youtube = new \Google_Service_YouTube($this->client);
        //
        //        if ($accessToken = $this->getLatestAccessTokenFromDB()) {
        //            $this->client->setAccessToken($accessToken);
        //        }
    }

    /**
     * Returns the last saved access token, if there is one, or null
     *
     * @return mixed
     */
    public function getLatestAccessTokenFromDB()
    {
        $service = WebService::where('name', StorageProvider::GOOGLE)->first();

        return $service ? $service->token : null;
        //return $latest ? (is_array($latest) ? $latest['access_token'] : $latest->access_token ) : null;
    }

    /**
     * Upload the video to YouTube
     *
     * @param  string  $path  The path to the file you wish to upload.
     * @param  array  $data  An array of data.
     * @param  string  $privacyStatus  The status of the uploaded video, set to 'public' by default.
     * @return self
     */
    public function upload($path, array $data, $privacyStatus = 'public')
    {

        /* ------------------------------------
        #. Setup the Snippet
        ------------------------------------ */
        $snippet = new \Google_Service_YouTube_VideoSnippet();

        if (array_key_exists('title', $data)) {
            $snippet->setTitle($data['title']);
        }
        if (array_key_exists('description', $data)) {
            $snippet->setDescription($data['description']);
        }
        if (array_key_exists('tags', $data)) {
            $snippet->setTags($data['tags']);
        }
        if (array_key_exists('category_id', $data)) {
            $snippet->setCategoryId($data['category_id']);
        }

        /* ------------------------------------
        #. Set the Privacy Status
        ------------------------------------ */
        $status = new \Google_Service_YouTube_VideoStatus();
        $status->privacyStatus = $privacyStatus;

        /* ------------------------------------
        #. Set the Snippet & Status
        ------------------------------------ */
        $video = new \Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        /* ------------------------------------
        #. Set the Chunk Size
        ------------------------------------ */
        $chunkSize = 1 * 1024 * 1024;

        /* ------------------------------------
        #. Set the defer to true
        ------------------------------------ */
        $this->client->setDefer(true);

        /* ------------------------------------
        #. Build the request
        ------------------------------------ */
        $insert = $this->youtube->videos->insert('status,snippet', $video, [
            //'onBehalfOfContentOwner' => 'wwSX8QxQGf3hB3EDQdACzQ',//$channel->user->service()?->client_id, //*Note:* This parameter is intended
            //'onBehalfOfContentOwnerChannel' => 'UCwwSX8QxQGf3hB3EDQdACzQ',//$channel->uuid, // This parameter can only be
        ]);

        /* ------------------------------------
        #. Upload
        ------------------------------------ */
        $media = new \Google_Http_MediaFileUpload(
            $this->client,
            $insert,
            'video/*',
            null,
            true,
            $chunkSize
        );

        /* ------------------------------------
        #. Set the Filesize
        ------------------------------------ */
        $media->setFileSize(filesize($path));

        /* ------------------------------------
        #. Read the file and upload in chunks
        ------------------------------------ */
        $status = false;
        $handle = fopen($path, 'rb');

        while (! $status && ! feof($handle)) {
            $chunk = fread($handle, $chunkSize);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);

        $this->client->setDefer(false);

        /* ------------------------------------
        #. Set the Uploaded Video ID
        ------------------------------------ */
        $this->videoId = $status['id'];

        return $this;
    }

    /**
     * Set a Custom Thumbnail for the Upload
     *
     * @param  string  $imagePath
     * @return self
     */
    public function withThumbnail($imagePath)
    {
        try {
            $videoId = $this->getVideoId();

            // Specify the size of each chunk of data, in bytes. Set a higher value for
            // reliable connection as fewer chunks lead to faster uploads. Set a lower
            // value for better recovery on less reliable connections.
            $chunkSizeBytes = 1 * 1024 * 1024;

            // Setting the defer flag to true tells the client to return a request which can be called
            // with ->execute(); instead of making the API call immediately.
            $this->client->setDefer(true);

            // Create a request for the API's thumbnails.set method to upload the image and associate
            // it with the appropriate video.
            $setRequest = $this->youtube->thumbnails->set($videoId);

            // Create a MediaFileUpload object for resumable uploads.
            $media = new \Google_Http_MediaFileUpload(
                $this->client,
                $setRequest,
                'image/png',
                null,
                true,
                $chunkSizeBytes
            );
            $media->setFileSize(filesize($imagePath));

            // Read the media file and upload it chunk by chunk.
            $status = false;
            $handle = fopen($imagePath, 'rb');
            while (! $status && ! feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }
            fclose($handle);

            // If you want to make other calls after the file upload, set setDefer back to false
            $this->client->setDefer(false);
            $this->thumbnailUrl = $status['items'][0]['default']['url'];

        } catch (\Google_Service_Exception $e) {
            exit($e->getMessage());
        } catch (\Google_Exception $e) {
            exit($e->getMessage());
        }

        return $this;
    }

    /**
     * Return the Video ID
     *
     * @return string
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * Return the URL for the Custom Thumbnail
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * Delete a YouTube video by it's ID.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete($id)
    {
        if (! $this->exists($id)) {
            return false;
        }

        $this->youtube->videos->delete($id);

        return true;
    }

    /**
     * Check if a YouTube video exists by it's ID.
     *
     * @param  int  $id
     * @return bool
     */
    public function exists($id)
    {
        $response = $this->youtube->videos->listVideos('status', ['id' => $id]);

        if (empty($response->items)) {
            return false;
        }

        return true;
    }

    /**
     * Pass method calls to the Google Client.
     *
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->client, $method], $args);
    }

    public function channel(): \Google\Service\YouTube\MemberListResponse
    {

        return $this->youtube->members->listMembers('snippet');
    }

    /**
     * @throws Exception
     */
    public function search(array $options, bool $toJson = true): \Google\Service\YouTube\SearchListResponse|array
    {
        $options = array_merge([
            'q' => '',
            'type' => 'video', // channel|playlist
            'order' => 'viewCount',
            'channelId' => '', // Filter on resources belonging to this channelId.
            'maxResults' => '50',
        ], $options);

        return $toJson ?
            $this->youtube->search->listSearch('snippet', array_filter($options))->getItems() :
            $this->youtube->search->listSearch('snippet', array_filter($options));
    }

    public function searchPlaylists(array $options, bool $toJson = true)
    {
        $options = array_merge([
            'id' => '', // Return the playlists with the given IDs for Stubby or
            'channelId' => '', //Return the playlists owned by the specified
            'maxResults' => '50',
        ], $options);

        return $toJson ? $this->youtube->playlists->listPlaylists('snippet', array_filter($options))->getItems() :
            $this->youtube->playlists->listPlaylists('snippet', array_filter($options));
    }

    public function searchChannels(array $options, bool $toJson = true)
    {
        $options = array_merge([
            'categoryId' => '',
            'maxResults' => '50',
            'id' => '',
        ], $options);

        return $toJson ? $this->youtube->channels
            ->listChannels('snippet', array_filter($options))->getItems() :
            $this->youtube->channels
                ->listChannels('snippet', array_filter($options));
    }
}
