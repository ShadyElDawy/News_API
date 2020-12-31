<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentsResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostsResource;
use App\Post;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use ApiResponseTrait;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //get all posts, attached wit it's comments and authors
        $posts = Post::with(['comments', 'author', 'category'])->paginate(15);

        //return $this->apiResponse(PostResource::collection($posts),404);

        //return new PostsResource($posts);
        return (new PostsResource($posts))
            ->response()
            ->setStatusCode(200);

        //return PostResource::collection($posts); also working
    }


    /**
     * @param Request $request
     * @return PostResource
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required',
        ]); //required that user pick category from droplist menu, each category will be linked to an id that exitst in db (e.g. 'sport' => '5')

          $post = new Post();
          $post->user_id = $request->user()->id; //returns user object -> id who creating the post from request (relationships)

        $post->title = $request->get('title');
        $post->content = $request->get('content');
        $post->category_id = $request->get('category_id');

        // $post = Post::create($request->all()); used to insert all data sent directly to db will work instead, if we falsed "stict" value in config/database

        //TODO handle 404 error
        if ($request->hasFile('featured_image')){
            $featuredImage = $request->file('featured_image'); //getting the image

            $filename= time().$featuredImage->getClientOriginalName(); //setting file name, with time+original file name
            Storage::disk('images')->putFileAs(
                $filename,
                $featuredImage,
                $filename
            ); //putFileAs function to stor uploaded files on disk(go to config/filesystem/images) with a given name

            $post->featured_image = url('/') .'/images/'.$filename; // goto file path and get the image from the path
        }

        $post->votes_up = 0;
        $post->votes_down = 0;
        $post->date_written = now();
        //$category = Category::find($post->category_id);
        //$Cattitle= $category->title;
        $post->save();
        /*
       $conc = DB::table('posts')
           ->join('categories', 'posts.category_id', '=', 'categories.id')
           ->select(['posts.title', 'posts.content','posts.category_id','posts.id', 'categories.title'])
           ->where('posts.category_id', '=',"$post->category_id")
           ->where('posts.title', '=',"$post->title")
           ->get();*/


        //return $this->apiResponse(new PostResource($post));
        return new PostResource($post);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show($id)
    {
        /*
        $post = Post::find($id);
        $comments = $post->comments;
        return new PostResource($post,$post->comments); //it's not a collection resource, but returns only one post/object
        //return response(["posts" => $post, "comments" => $post->comments],200); //also working
        */
        if (!Post::find($id)){
            return $this->apiResponse(null,"not found",404);
        }
        $post = Post::with(['comments', 'author', 'category'])->where('id',$id)->get();
        return $this->apiResponse(new PostResource($post));
    }

    /**
     * @param $id
     * @return CommentsResource
     */

    public function comments($id){
        $post = Post::find($id);
        if (!$post){
            return $this->apiResponse(null,"not found",404);
        }
        $comments = $post->comments()->paginate(15);
        return new CommentsResource($comments);
    }
    /**
     * @param Request $request
     * @param $id
     * @return PostResource
     */
    public function update(Request $request, $id)
    {
        //no validation, it's optional to update or not
        $user= $request->user(); //returns user object who creating the post from request (relationships)
        $post = Post::find($id);
        if (!$post){ //if not found
            return $this->apiResponse(null,"not found",404);
        }
        abort_if($post->user_id !== auth()->id(), 403); //to check if the current user who is updating the post is the real owner of the post
        $post->update($request->all()); //update data in db with data given in request (input) directly

//        //if user Updated title
//        if($request->has('title')){
//            $post->title = $request->get('title');
//        }
//        //if user updated content
//        if($request->has('content')){
//            $post->content = $request->get('content');
//        }
//        //if user updated category
//        if($request->has('category_id')){
//            $post->category_id = $request->get('category_id');
//        }

        //TODO handle 404 error
        if ($request->hasFile('featured_image')){
            $featuredImage = $request->file('featured_image'); //getting the image

            $filename= time().$featuredImage->getClientOriginalName(); //setting file name, with time+original file name
            Storage::disk('images')->putFileAs(
                $filename,
                $featuredImage,
                $filename
            ); //putFileAs function to stor uploaded files on disk(go to config/filesystem/images) with a given name

            $post->featured_image = url('/') .'/images/'.$filename; // goto file path and get the image from the path
        }
        $post->save();
        /*
       $conc = DB::table('posts')
           ->join('categories', 'posts.category_id', '=', 'categories.id')
           ->select(['posts.title', 'posts.content','posts.category_id','posts.id', 'categories.title'])
           ->where('posts.category_id', '=',"$post->category_id")
           ->where('posts.title', '=',"$post->title")
           ->get();*/
        return new PostResource($post);
    }

    /**
     * @param Request $request
     * @param $id
     * @return PostResource
     */
    public function votes(Request $request, $id){
        //get the post id, user input and check whether he voted up or down
        $request->validate([
           'vote' => 'required',
        ]);
        $post = Post::find($id);
        $voters_down = json_decode($post->voters_down); //post->voters_up of post is json, decode it into array to be able to search into it
        $voters_up = json_decode($post->voters_up);  //same

        if($voters_up == null){ //to skip null error in an_array func
            $voters_up = [];
        }
        if($voters_down == null){
            $voters_down = [];
        }

            if (!((in_array($request->user()->id, $voters_up)) || (in_array($request->user()->id, $voters_down)))){     ///if user not in post's voters, do the vote method
                switch ($request->get('vote')){
                    case 'up':         //if user picked up, add one to this post's votes_up
                        $post->votes_up += 1;
                        array_push($voters_up, $request->user()->id);       //then add user to voters_up of this post
                        $post->voters_up = json_encode($voters_up);        //convert voters back into json and store it into post's voters
                        $post->save();
                        break;

                    case 'down':            //if user picked down, add one to this post's votes_down
                        $post->votes_down += 1; //if user picked down, add one to this post's votes_down
                        array_push($voters_down, $request->user()->id);
                        $post->voters_down = json_encode($voters_down);
                        $post->save();
                        break;
                }
            }
        return $this->apiResponse(new PostResource($post));
    }


    public function destroy($id)
    {

        $post = auth()->user()->posts->find($id); // allow logged in user to search only his own posts not all db, so that he can delete only his own posts,
        if (!$post){
            return $this->apiResponse(null,"not found",404);
        }
        $post->delete(); //destroy post
        return $this->apiResponse(new PostResource($post));
    }
}
