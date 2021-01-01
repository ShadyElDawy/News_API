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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * @return UsersResource
     */
    public function index()
    {
        $users = User::all();
        return $this->apiResponse(new UsersResource($users));
    }

    /**
     * @param Request $request
     * @return UserResource
     */
    public function store(Request $request)
    {
        //validate user inputs
        $request->validate([
            'name' => 'required',
           'email'=>'required',
           'password'=> 'required',
        ]); //once we create this user, observer will generate api_token auto for him

        $user = new User(); //create new user
        $user->name = $request->get('name'); //set new user name as user's input in request
        $user->email = $request->get('email'); //set user email as user's input
        $user->password = Hash::make($request->get('password')); //get user password and hash it into db

        $user->save(); //save user to database
        return $this->apiResponse(new UserResource($user),201); //return user object data(email, name etc) and post it to route register
    }

    /**
     * @param $id
     * @return UserResource
     */
    public function show($id) //this param is what is in route {id}
    {
        $user = User::find($id);
        if (!$user){
            return $this->apiResponse(null,"not found",404);
        }

        return $this->apiResponse(new UserResource($user));

    }

    /**
     * @param $id
     * @return AuthorPostsResource
     */
    public function posts($id)
    {
        $user = User::find($id);  //get the object
        if (!$user){
            return $this->apiResponse(null,"not found",404);
        }
        $posts = $user->posts()->paginate(5); //posts into obeject
        return new AuthorPostsResource($posts); //can use UserResource but it doesn't return links and meta as it's not collection, so no pagination
    }


    /**
     * @param $id
     * @return AuthorCommentsResource
     */
    public function comments($id){
        $user = User::find($id);
        if (!$user){
            return $this->apiResponse(null,"not found",404);
        }
        $comments = $user->comments()->paginate(15);
        return new AuthorCommentsResource($comments);
    }

    /**
     * @param Request $request
     * @param $id
     * @return UserResource
     */
    public function update(Request $request, $id)
    {

        $user = User::find($id);
        abort_if($user->id !== auth()->id(), 403,"unauthorized to perform that action"); //to prevent another user to update other's users

        if($request->has('password')){
            $user->password = Hash::make($request->get('password')); //if user updated password hash it and merge to request
        }
        $request->merge(['password' => $user->password]);
        $user->update($request->all()); ////update data in db with data given in request (input) directly

        if ($request->hasFile('avatar')){
            $featuredImage = $request->file('avatar'); //getting the image

            $filename= time().$featuredImage->getClientOriginalName(); //setting file name, with time+original file name
            Storage::disk('images')->putFileAs(
                $filename,
                $featuredImage,
                $filename
            ); //putFileAs function to stor uploaded files on disk(go to config/filesystem/images) with a given name

            $user->avatar = url('/') .'/images/'.$filename; // goto file path and get the image from the path
        }
        $user->update($request->all()); ////update data in db with data given in request (input) directly
        //$user->save(); //save to database
        return $this->apiResponse(new UserResource($user));

    }

    /**
     * @param Request $request
     * @return TokenResource|string
     */
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
        return $this->apiResponse(null,"not found",404);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {

        if (auth()->user()) {
            $user = auth()->user(); //current user
            $user->api_token = null; // clear api token, you need to false "strict" in /config/database.php
            $user->api_token = bin2hex(openssl_random_pseudo_bytes(30)); //regenerate token
            $user->save();

            return response()->json([
                'message' => 'Thank you for using our application',
            ]);
        }
        return response()->json([
            'error' => 'Unable to logout user',
            'code' => 401,
        ], 401);
    }



}
