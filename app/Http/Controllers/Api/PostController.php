<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentsResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostsResource;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

    /**
     * @return PostsResource
     */
    public function index()
    {
        return new PostsResource(Post::paginate(10));
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
        $user= $request->user(); //returns user who creating the post (relationships)
        $post = new Post();
        $post->user_id = $user->id;
        $post->title = $request->get('title');
        $post->content = $request->get('content');
        $post->category_id = $request->get('category_id');


        //TODO featured_image will be uploaded using File Upload later
        $post->votes_up = 0;
        $post->votes_down = 0;
        $post->date_written= now();

        $category = Category::find($post->category_id);
        $Cattitle= $category->title;
        $post->save();
//       $conc = DB::table('posts')
//           ->join('categories', 'posts.category_id', '=', 'categories.id')
//           ->select(['posts.title', 'posts.content','posts.category_id','posts.id', 'categories.title'])
//           ->where('posts.category_id', '=',"$post->category_id")
//           ->where('posts.title', '=',"$post->title")
//           ->get();

        //return response(['data'=>$post, 'title'=> "$Cattitle"],200); //to return data and catagory name
        //return new PostResource([$post, 'title'=> "$Cattitle"]); //alsso working
        return new PostResource($post);
    }

    /**
     * @param $id
     * @return PostResource
     */
    public function show($id)
    {
        $post = Post::find($id);
        return new PostResource($post); //it's not a collection resource but returns only one post/object
    }

    /**
     * @param $id
     * @return CommentsResource
     */

    public function comments($id){
        $post = Post::find($id);
        $comments = $post->comments()->paginate(15);
        return new CommentsResource($comments);
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
