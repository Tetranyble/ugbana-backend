<?php

use App\Models\User;
use App\services\FileSystem;
use App\services\ResumeParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Masih\YoutubeDownloader\YoutubeDownloader;
use Shifft\CsvParser\CsvParser;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('download', function (Request $request,ResumeParser $parser, FileSystem $client){

    $resume = $parser->parse(
        $client->show(
            'resumes/leonard-ekenekiso-bullet-resume.pdf',
            \App\Enums\StorageProvider::S3PUBLIC
        )
    );

    $user = \App\Models\User::find(1);
    $user->fill([
        'name' => $resume->name,
        'email' => $resume->email,
    ])->save();
    return $user->profile()->create([
        'skills' => $resume->skills,
        'education' => $resume->education,
        'job_experience' => $resume->experience
    ]);


});
/**
 * Cloud storage Authentication Routes
 * Example Google Drive,
 */
Route::group(['prefix' => 'services'], function () {
    Route::get('connect/{service}', [\App\Http\Controllers\Api\ServiceAuthenticationController::class, 'connect'])
        ->name('services.connect');

    Route::get('authorization/{service}', [\App\Http\Controllers\Api\ServiceAuthenticationController::class, 'authorization'])
        ->name('services.authorization');

});

Route::get('youtube', function (){

    $yt = new YoutubeDl();
    $channel = \App\Models\ChannelVideo::where('uuid', 'oDAw7vW7H0c')->first();
    $collection = $yt->download(
        Options::create()
            ->geoByPass()
            ->output('%(id)s.%(ext)s')
            //->noPart(true)
            ->downloadPath(storage_path('app/public/video/downloads'))
            ->url("https://www.youtube.com/watch?v=oDAw7vW7H0c")
    );

    foreach ($collection->getVideos() as $video) {
        if ($video->getError() !== null) {
            echo "Error downloading video: {$video->getError()}.";
        } else {
            return
                [
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
                ];

        }
    }
    return 'done';
});

Route::get('channel', function (\App\services\Google\Youtube $youtube){
//    return $youtube->search([
//        'q' => 'nollywood movies',
//        'type' => 'video',
//    ]);
    $user = User::where('email','movieswebbs@gmail.com')->first();
    return $user->storeSearch([
        'q' => 'nollywood movies',
        'type' => 'video',
    ]);
});
Route::get('channels', function (\App\services\Google\Youtube $youtube){
//    return $youtube->searchChannels([
//        'id' => 'UCulcQNJeXMh38b8kl8VHxfg,UCypAoMCRQuNL2RBwy-x4oQg',
//    ]);
    $user = User::where('email','movieswebbs@gmail.com')->first();
    return $user->storeChannels('UC_e-1gI4D1aOixooUVkUzXg,UC_e-1gI4D1aOixooUVkUzXg,UC-q-khKtmZbqknICbo3H-GA');
});

Route::get('upload', function (){
    $user = User::where('email','senenerst@gmail.com')->first();
//    $channel = \App\Models\Channel::where('user_id', $user->id)
//        ->where('id', 33)
//        ->first();
//
//    return $channel->user->service();
    $user->upload($user->owesChannel(),
        \App\Models\ChannelVideo::first()
    );
});
