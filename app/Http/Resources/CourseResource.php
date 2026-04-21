<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LessonResource;

class CourseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'level' => $this->level,
            'price' => $this->price,
            'quota' => $this->quota,
            'status' => $this->status,
            'thumbnail_url' => $this->thumbnail_url,
            'rating_count' => $this->rating_count,
            'rating_avg' => $this->rating_avg,
            'created_at' => $this->created_at,
            'categories' => CategoryResource::collection(
                $this->whenLoaded('categories')
            ),
            'lessons' => LessonResource::collection(
                $this->whenLoaded('lessons')
            ),
        ];
    }
}
