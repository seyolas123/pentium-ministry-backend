<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'audio' => $this->audio,
            'author' => $this->author,
            'cover_img' => $this->cover_img,
            
            
        ];
    }
}
