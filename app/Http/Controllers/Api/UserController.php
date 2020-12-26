<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorCommentsResource;
use App\Http\Resources\AuthorPostsResource;
use App\Http\Resources\UsersResource;
use App\Http\Resources\UserResource;

use App\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = \App\User::all();
        return new \App\Http\Resources\UsersResource($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) //this param is what is in route {id}
    {
        return new UserResource(User::find($id));
    }

    /**
     * @param $id
     * @return AuthorPostsResource
     */
    public function posts($id)
    {
        $user = User::find($id);  //get the object
        $posts = $user->posts()->paginate(5); //posts into obeject
        return new AuthorPostsResource($posts); //can use UserResource but it doesn't return links and meta as it's not collection, so no pagination
    }


    /**
     * @param $id
     * @return AuthorCommentsResource
     */
    public function comments($id){
        $user = User::find($id);
        $comments = $user->comments()->paginate(15);
        return new AuthorCommentsResource($comments);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
