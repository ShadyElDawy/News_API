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
//            'content' => $this->content,
//            'user_id' => $this->user_id,
//            'username'=> $this->user->name,
//            'category_id' => $this->category_id,
//        ];
    }
}
