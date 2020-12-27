<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);

//        return [
//            'id' => $this->id,
//            'title' => $this->title,
//            'content' => $this->content,
//            'user_id' => $this->user_id,
//            'category_id' => $this->category_id,
//            'category_title' => $this->cattitle,
//
//        ];
    }
}
