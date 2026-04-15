<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'instructor';
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:draft,published,archived',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status course wajib diisi.',
            'status.string' => 'Status course harus berupa teks.',
            'status.in' => 'Status course harus draft, published, atau archived.',
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'status course',
        ];
    }
}
