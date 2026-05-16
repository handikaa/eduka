<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCourseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'status' => 'nullable|in:draft,published,archived',

            'thumbnail_url' => 'nullable|string',
            'thumbnail' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'lessons' => 'nullable|array',

            'lessons.*.id' => 'nullable|integer|exists:lessons,id',
            'lessons.*.title' => 'required|string|max:255',
            'lessons.*.type' => 'required|in:video,content,file',
            'lessons.*.content' => 'nullable|string',

            'lessons.*.video_url' => 'nullable|string',
            'lessons.*.file_url' => 'nullable|string',

            'lessons.*.video_file' => [
                'nullable',
                'file',
                'mimetypes:video/mp4',
                'max:102400',
            ],

            'lessons.*.file' => [
                'nullable',
                'file',
                'max:20480',
            ],

            'lessons.*.is_preview' => 'nullable|boolean',
            'lessons.*.position' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'thumbnail.image' => 'Thumbnail harus berupa file gambar.',
            'thumbnail.mimes' => 'Thumbnail hanya boleh berupa jpg, jpeg, png, atau webp.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 5MB.',

            'lessons.*.type.in' => 'Tipe lesson harus video, content, atau file.',

            'lessons.*.video_file.file' => 'Video harus berupa file.',
            'lessons.*.video_file.mimetypes' => 'Video hanya boleh berupa mp4.',
            'lessons.*.video_file.max' => 'Ukuran video maksimal 100MB.',

            'lessons.*.file.file' => 'File materi harus berupa file.',
            'lessons.*.file.max' => 'Ukuran file materi maksimal 20MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $lessons = $this->input('lessons', []);

            $previewCount = collect($lessons)
                ->filter(fn ($lesson) => filter_var(
                    $lesson['is_preview'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                ))
                ->count();

            if ($previewCount > 1) {
                $validator->errors()->add(
                    'lessons',
                    'Hanya boleh ada 1 lesson yang menjadi preview.'
                );
            }

            foreach ($lessons as $index => $lesson) {
                $id = $lesson['id'] ?? null;
                $type = $lesson['type'] ?? null;
                $content = $lesson['content'] ?? null;

                $videoUrl = $lesson['video_url'] ?? null;
                $fileUrl = $lesson['file_url'] ?? null;

                $videoFile = $this->file("lessons.$index.video_file");
                $file = $this->file("lessons.$index.file");

                if ($type === 'content' && blank($content)) {
                    $validator->errors()->add(
                        "lessons.$index.content",
                        'Content wajib diisi untuk lesson bertipe content.'
                    );
                }

                if ($type === 'video') {
                    if (!$id && !$videoFile) {
                        $validator->errors()->add(
                            "lessons.$index.video_file",
                            'Video file wajib diisi untuk lesson video baru.'
                        );
                    }

                    if ($id && blank($videoUrl) && !$videoFile) {
                        $validator->errors()->add(
                            "lessons.$index.video_file",
                            'Video URL lama atau video file baru wajib diisi untuk lesson bertipe video.'
                        );
                    }
                }

                if ($type === 'file') {
                    if (!$id && !$file) {
                        $validator->errors()->add(
                            "lessons.$index.file",
                            'File wajib diisi untuk lesson file baru.'
                        );
                    }

                    if ($id && blank($fileUrl) && !$file) {
                        $validator->errors()->add(
                            "lessons.$index.file",
                            'File URL lama atau file baru wajib diisi untuk lesson bertipe file.'
                        );
                    }
                }
            }
        });
    }
}