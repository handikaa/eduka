<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->content,
            'video_url' => $this->video_url,
            'is_preview' => $this->is_preview,
            'position' => $this->position,
            'file_url' => $this->file_url, 
            'created_at' => $this->created_at,
        ];
    }
}
