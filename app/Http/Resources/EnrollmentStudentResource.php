<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'enrollment' => [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'course_id' => $this->course_id,
                'status' => $this->status,
                'enrolled_at' => $this->enrolled_at,
                'completed_at' => $this->completed_at,
            ],
        ];
    }
}
