<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'enrolled_at' => $this->enrolled_at,
            'completed_at' => $this->completed_at,
            'course' => [
                'id' => $this->course?->id,
                'title' => $this->course?->title,
                'slug' => $this->course?->slug,
                'thumbnail_url' => $this->course?->thumbnail_url,
                'price' => $this->course?->price,
                'status' => $this->course?->status,
            ],
        ];
    }
}
