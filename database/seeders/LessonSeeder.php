<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Seed the lessons table with lessons for each course.
     * Each course will have at least 3 lessons with different types.
     */
    public function run(): void
    {
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->warn('No courses found. Skipping lesson seeding.');
            return;
        }

        foreach ($courses as $course) {
            // Create 5 lessons per course
            $this->createLessonsForCourse($course);
        }

        $this->command->info(sprintf('%d courses seeded with lessons successfully!', count($courses)));
    }

    /**
     * Create lessons for a specific course.
     */
    private function createLessonsForCourse(Course $course): void
    {
        $lessonTypes = ['video', 'text', 'quiz', 'assignment'];
        $lessonTitles = [
            'Introduction to the Course',
            'Core Concepts and Fundamentals',
            'Hands-on Practical Examples',
            'Advanced Techniques and Best Practices',
            'Project and Final Assessment',
        ];

        $videoUrls = [
            'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'https://example.com/video1.mp4',
            'https://example.com/video2.mp4',
            'https://example.com/video3.mp4',
            'https://example.com/video4.mp4',
        ];

        $fileUrls = [
            'https://example.com/resources.pdf',
            'https://example.com/cheatsheet.pdf',
            'https://example.com/exercise.zip',
            'https://example.com/documentation.zip',
            'https://example.com/project-template.zip',
        ];

        for ($i = 0; $i < 5; $i++) {
            $type = $lessonTypes[$i % count($lessonTypes)];
            $isPreview = $i === 0; // First lesson is always preview

            $data = [
                'course_id' => $course->id,
                'title' => $lessonTitles[$i],
                'type' => $type,
                'content' => $this->getContentForType($type),
                'video_url' => ($type === 'video') ? $videoUrls[$i] : null,
                'file_url' => ($i % 2 === 0) ? $fileUrls[$i] : null,
                'order_index' => $i + 1,
                'is_preview' => $isPreview,
            ];

            Lesson::create($data);
        }
    }

    /**
     * Generate appropriate content based on lesson type.
     */
    private function getContentForType(string $type): string
    {
        $contents = [
            'video' => 'Di pelajaran ini, Anda akan menonton video yang menjelaskan konsep-konsep penting. Ikuti setiap langkah dengan seksama dan catat poin-poin penting.',
            'text' => 'Materi pembelajaran dalam format teks yang komprehensif. Bacalah seluruh konten dan pahami setiap bagian. Jangan ragu untuk membaca ulang bagian yang sulit dipahami.',
            'quiz' => 'Ujian untuk menguji pemahaman Anda tentang materi yang telah dipelajari. Jawab semua pertanyaan dengan jujur. Skor minimal 70% diperlukan untuk lulus.',
            'assignment' => 'Tugas praktik untuk menerapkan pengetahuan yang telah Anda pelajari. Selesaikan tugas ini sesuai dengan instruksi dan submit hasilnya untuk evaluasi.',
        ];

        return $contents[$type] ?? 'Konten pembelajaran untuk bagian ini.';
    }
}
