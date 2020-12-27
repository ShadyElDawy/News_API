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

/** @user related
 * */
Route::get('/authors', 'Api\\UserController@index');
Route::get('/authors/{id}', 'Api\\UserController@show'); //takes param
Route::get('posts/authors/{id}', 'Api\\UserController@posts'); //takes param
Route::get('comments/authors/{id}', 'Api\\UserController@comments');

// end of User Related

/** @Post related
 * */

Route::get('categories', 'Api\CategoryController@index');
Route::get('posts/categories/{id}', 'Api\CategoryController@posts');
Route::get('posts', 'Api\PostController@index');
Route::get('posts/{id}', 'Api\PostController@show');
Route::get('comments/posts/{id}', 'Api\PostController@comments');

// end of post related

Route::post('register', 'api\UserController@store');
Route::post('token', 'api\UserController@getToken');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
