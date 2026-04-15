<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()?->role === 'instructor';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'instructor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'thumbnail_url' => 'nullable|url',
            'lessons' => 'nullable|array',
            'lessons.*.title' => 'required|string',
            'lessons.*.content' => 'required|string',

            'categories' => 'required|array|min:1',
            'categories.*' => 'integer|distinct|exists:categories,id',

        ];
    }
}
