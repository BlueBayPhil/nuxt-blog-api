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

// Authentication routes.
Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');

// Public post routes.
Route::get('/posts', 'PostsController@index');
Route::get('/posts/{post}', 'PostsController@show');

// Public comment routes.
Route::get('/posts/{post}/comments', 'CommentsController@index');
Route::get('/comments', 'CommentsController@index');
Route::get('/comments/{comment?}', 'CommentsController@show');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/posts', 'PostsController@store');
    Route::post('/posts/{post}/comments', 'CommentsController@store');

    Route::delete('/logout', 'AuthController@logout');
});
