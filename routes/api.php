<?php

use Illuminate\Http\Request;


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
// once we are in api file not web, so our namespace would be api/users
Route::get('/authors', 'Api\\UserController@index');
Route::get('/authors/{id}', 'Api\\UserController@show'); //takes param
Route::get('posts/authors/{id}', 'Api\\UserController@posts'); //takes param
Route::get('comments/authors/{id}', 'Api\\UserController@comments');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
