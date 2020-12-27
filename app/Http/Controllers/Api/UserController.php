<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorCommentsResource;
use App\Http\Resources\AuthorPostsResource;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UsersResource;
use App\Http\Resources\UserResource;

use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function MongoDB\BSON\toJSON;

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

    //after submitting form, or sending params to route (using postman) all values entered by user get stored in Request object, email and password
    public function getToken(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]); //validations of user inputs
        $credentials = $request -> only('email','password'); //request has a lot of other info that we don't need
        if (Auth::attempt($credentials)){
            $user = User::where('email', $request->get('email'))->first(); //if login true, grap this user using the email he logged in with
            return new TokenResource(['token'=>$user->api_token]); //then api_token gets posted to route we specified, format is to return as json not string.
        }
        return 'not found';
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
