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
        return new UsersResource($users);
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
        return new UserResource($user); //return user object data(email, name etc) and post it to route register
    }

    /**
     * @param $id
     * @return UserResource
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
     * @param Request $request
     * @param $id
     * @return UserResource
     */
    public function update(Request $request, $id)
    {
        //mail can't be changed
        //check if
        $user = User::find($id);
        abort_if($user->id !== auth()->id(), 403); //to prevent another user to update other's users

//        if($request->has('name')){
//            $user->name=$request->get('name');
//        }
//        if($request->has('avatar')){
//            $user->avatar = $request->get('avatar');
//        }
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

        $user->save(); //save to database
        return new UserResource($user);

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
