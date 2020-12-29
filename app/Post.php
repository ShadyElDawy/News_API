<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
    	'title', 'content', 'date_written', 'featured_image', 'votes_up', 'votes_down', 'user_id', 'category_id','voters_up','voters_down'

    ];

    //Author relationship in Post model doesn’t have the same column name in post table to make relationship(which is user_id),
    // So we have to specify foreign key and the real owner column in user table(which is id)
    public function author(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

}
