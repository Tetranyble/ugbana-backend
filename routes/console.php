<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('pint:clean', function () {
    $this->comment('Reformat and remove unused imports...');
    Process::run('php ./vendor/bin/pint', function (string $type, string $output) {
        echo $output;
    });
    $this->comment('Done!');
})->describe('Reformat and remove unused import.');
Artisan::command('youtube:download', function () {
    \App\Models\ChannelVideo::orderBy('id')
        ->where('user_id', 1)
        ->chunkMap(function ($video) {
            \App\Jobs\DownloadYoutube::dispatch($video);
        }, 10);
    //    \App\Jobs\DownloadYoutube::dispatch(
    //        \App\Models\ChannelVideo::first()
    //    );

});

Artisan::command('youtube:upload', function () {
    //    $videos = \App\Models\ChannelVideo::orderBy('id')
    //        ->limit(5)->get();
    //    $videos->map(function ($video){
    //        \App\Jobs\YoutubeUpload::dispatch($video);
    //    });
    \App\Jobs\YoutubeUpload::dispatch(
        \App\Models\ChannelVideo::find(101)
    );

});
