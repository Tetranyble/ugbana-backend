<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('v1.')->prefix('v1')->group(function () {
    Route::post('signup', \App\Http\Controllers\Api\RegistrationController::class)
        ->name('signup')
        ->middleware('guest:api');

    Route::post('/login', \App\Http\Controllers\Api\AuthenticationController::class)
        ->middleware('guest:api')
        ->name('login');

    Route::post('users/resumes', [\App\Http\Controllers\Api\ProfileDocumentController::class, 'store'])
        ->name('resumes.store')
        ->middleware(['permissions:userprofile_store', 'auth:api']);

    Route::get('users/resumes', [\App\Http\Controllers\Api\ProfileDocumentController::class, 'show'])
        ->name('resumes.show')
        ->middleware(['permissions:userprofile_show', 'auth:api']);

    Route::get('users/profile', \App\Http\Controllers\Api\ProfileController::class)
        ->middleware('auth:api')
        ->name('users.profile');
});
