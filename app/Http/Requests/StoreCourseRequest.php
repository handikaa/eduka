<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return $this->user()?->role === 'instructor';
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',

            'thumbnail' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', // 5MB
            ],

            'categories' => 'required|array|min:1',
            'categories.*' => 'integer|distinct|exists:categories,id',

            'lessons' => 'nullable|array',

            'lessons.*.title' => 'required|string|max:255',
            'lessons.*.type' => 'required|in:video,content,video_content',

            // content nullable secara umum,
            // nanti diwajibkan secara conditional berdasarkan type
            'lessons.*.content' => 'nullable|string',

            // untuk video via URL eksternal, misalnya YouTube/embed/mp4 URL
            'lessons.*.video_url' => 'nullable|url',

            // untuk video upload lokal
            'lessons.*.video_file' => [
                'nullable',
                'file',
                'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska',
                'max:20480', // 20MB
            ],

            'lessons.*.is_preview' => 'nullable|boolean',
        ];
    }
    public function messages()
    {
        return [
            'thumbnail.image' => 'Thumbnail harus berupa file gambar.',
            'thumbnail.mimes' => 'Thumbnail hanya boleh berupa jpg, jpeg, png, atau webp.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 5MB.',

            'lessons.*.type.in' => 'Tipe lesson harus video, content, atau video_content.',
            'lessons.*.video_url.url' => 'Video URL harus berupa URL yang valid.',
            'lessons.*.video_file.file' => 'Video harus berupa file.',
            'lessons.*.video_file.mimetypes' => 'Video hanya boleh berupa mp4, mov, avi, atau mkv.',
            'lessons.*.video_file.max' => 'Ukuran video maksimal 20MB.',
        ];
    }



    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $lessons = $this->input('lessons', []);

            foreach ($lessons as $index => $lesson) {
                $type = $lesson['type'] ?? null;
                $content = $lesson['content'] ?? null;
                $videoUrl = $lesson['video_url'] ?? null;
                $videoFile = $this->file("lessons.$index.video_file");

                if ($type === 'content' && blank($content)) {
                    $validator->errors()->add(
                        "lessons.$index.content",
                        'Content wajib diisi untuk lesson bertipe content.'
                    );
                }

                if ($type === 'video') {
                    if (!$videoUrl && !$videoFile) {
                        $validator->errors()->add(
                            "lessons.$index.video_file",
                            'Video file atau video URL wajib diisi untuk lesson bertipe video.'
                        );
                    }

                    if ($videoUrl && $videoFile) {
                        $validator->errors()->add(
                            "lessons.$index.video_file",
                            'Pilih salah satu: video URL atau video file, tidak boleh keduanya.'
                        );
                    }
                }

                if ($type === 'video_content') {
                    if (blank($content)) {
                        $validator->errors()->add(
                            "lessons.$index.content",
                            'Content wajib diisi untuk lesson bertipe video_content.'
                        );
                    }

                    if (!$videoUrl && !$videoFile) {
                        $validator->errors()->add(
                            "lessons.$index.video_file",
                            'Video file atau video URL wajib diisi untuk lesson bertipe video_content.'
                        );
                    }

                    if ($videoUrl && $videoFile) {
                        $validator->errors()->add(
                            "lessons.$index.video_file",
                            'Pilih salah satu: video URL atau video file, tidak boleh keduanya.'
                        );
                    }
                }
            }
        });
    }
}
