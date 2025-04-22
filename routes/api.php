<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the 'api' middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/users', [\App\Http\Controllers\UserController::class, 'register']);
Route::post('/users/login', [\App\Http\Controllers\UserController::class, 'login']);

Route::middleware([\App\Http\Middleware\ApiAuthMiddleware::class])->group(function () {
    Route::get('/users/current', [\App\Http\Controllers\UserController::class, 'getCurrenUser']);
    Route::patch('/users/current', [\App\Http\Controllers\UserController::class, 'updateUser']);
    Route::delete('users/logout', [\App\Http\Controllers\UserController::class, 'logout']);

    Route::post('/contacts', [\App\Http\Controllers\ContactController::class, 'createContact']);
    Route::get('/contacts', [\App\Http\Controllers\ContactController::class, 'searchContact']);
    Route::get('/contacts/{id:}', [\App\Http\Controllers\ContactController::class, 'detailContact'])->where('id', '[0-9]+');
    Route::put('/contacts/{id:}', [\App\Http\Controllers\ContactController::class, 'updateContact'])->where('id', '[0-9]+');
    Route::delete('/contacts/{id:}', [\App\Http\Controllers\ContactController::class, 'deleteContact'])->where('id', '[0-9]+');

    Route::post('/contacts/{id:}/addresses', [\App\Http\Controllers\AddressController::class, 'createAddress'])
        ->where('id', '[0-9]+');
    Route::get('/contacts/{idContact:}/addresses', [\App\Http\Controllers\AddressController::class, 'listAddress'])
        ->where('idContact', '[0-9]+');
    Route::get('/contacts/{idContact:}/addresses/{idAddress:}', [\App\Http\Controllers\AddressController::class, 'getAddress'])
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
    Route::put('/contacts/{idContact:}/addresses/{idAddress:}', [\App\Http\Controllers\AddressController::class, 'updateAddress'])
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
    Route::delete('/contacts/{idContact:}/addresses/{idAddress:}', [\App\Http\Controllers\AddressController::class, 'deleteAddress'])
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
});
