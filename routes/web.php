<?php

use App\Models\User;
use App\services\FileSystem;
use App\services\ResumeParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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


Route::get('youtube', function (){

    $yt = new YoutubeDl();
    //$channel = \App\Models\ChannelVideo::where('uuid', 'oDAw7vW7H0c')->first();
    $collection = $yt->setBinPath(config('pensuh.binary.ytdlp'))
        ->download(
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
                \App\Models\ChannelVideo::create([
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
                    'user_id' => 1,
                    'uuid' => \Illuminate\Support\Str::uuid()->toString().'-1',
            ]);

        }
    }
    return 'done';
});

Route::get('channel', function (\App\services\Google\Youtube $youtube){


    return auth('web')->user()->storeSearch([
        'q' => 'nollywood movies',
        'type' => 'video',
    ]);
})->middleware('auth');
Route::get('channels', function (\App\services\Google\Youtube $youtube){

    return $channels = auth('web')->user()->load('channels');
    $user = User::where('email','senenerst@gmail.com')->with('channels')->first();
    $channelsUUid = $user->channels->pluck('uuid')->implode(',');
    return $user->storeChannels($channelsUUid);
});

Route::get('upload', function (){
    $user = auth('web')->user();
    $user->upload(\App\Models\ChannelVideo::first());
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
/**
 * Cloud storage Authentication Routes
 * Example Google Drive,
 */
Route::middleware('auth')->prefix('services')->group( function () {
    Route::get('connect/{service}', [\App\Http\Controllers\Api\ServiceAuthenticationController::class, 'connect'])
        ->name('services.connect');

    Route::get('authorization/{service}', [\App\Http\Controllers\Api\ServiceAuthenticationController::class, 'authorization'])
        ->name('services.authorization');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
