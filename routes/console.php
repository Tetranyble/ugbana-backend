<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Artisan::command('youtube:download', function (){
    \App\Models\ChannelVideo::orderBy('id')
        ->limit(5)
        ->chunkMap(function ($video){
            \App\Jobs\DownloadYoutube::dispatch($video);
        });

});

Artisan::command('youtube:upload', function (){
    $videos = \App\Models\ChannelVideo::orderBy('id')
        ->limit(5)->get();
    $videos->map(function ($video){
        \App\Jobs\YoutubeUpload::dispatch($video);
    });

});
