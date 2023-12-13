<?php

use App\services\Clients\FileSystem;
use App\services\ResumeParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Shifft\CsvParser\CsvParser;

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
