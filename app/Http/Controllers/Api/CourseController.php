<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistance\Eloquent\Models\Course;
use App\Infrastructure\Persistance\Eloquent\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CourseController extends Controller
{
    /**
     * Get all courses with instructor and category info
     * GET /api/v1/courses
     */
    public function index(Request $request)
    {
        try {
            $query = Course::with(['instructor:id,name,email', 'categories:id,name']);

            // Filter by search
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }

            // Filter by level
            if ($request->has('level')) {
                $query->where('level', $request->get('level'));
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->whereHas('categories', function ($q) {
                    $q->where('category_id', request()->get('category_id'));
                });
            }

            // Pagination
            $courses = $query->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'message' => 'Daftar kursus berhasil diambil',
                'data' => $courses->items(),
                'pagination' => [
                    'total' => $courses->total(),
                    'per_page' => $courses->perPage(),
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar kursus',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get course detail by ID
     * GET /api/v1/courses/{id}
     */
    public function show($id)
    {
        try {
            $course = Course::with([
                'instructor:id,name,email,avatar_url',
                'categories:id,name',
                'lessons' => function ($query) {
                    // Explicitly select columns and handle soft deletes properly
                    $query->select('id', 'course_id', 'title', 'type', 'content', 'video_url', 'file_url', 'order_index', 'is_preview', 'created_at', 'updated_at', 'deleted_at')
                        ->whereNull('deleted_at');
                }
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail kursus berhasil diambil',
                'data' => [
                    'course' => $course,
                    'lessons_count' => $course->lessons()->count(),
                    'enrollments_count' => $course->enrollments()->count(),
                    'reviews_count' => $course->reviews()->count(),
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail kursus',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new course with lessons (Only Instructor)
     * POST /api/v1/courses
     * 
     * Request body example:
     * {
     *   "title": "PHP Laravel Master",
     *   "slug": "php-laravel-master",
     *   "description": "Pelajari Laravel dari dasar hingga mahir",
     *   "level": "beginner",
     *   "price": 299000,
     *   "quota": 50,
     *   "status": "published",
     *   "category_ids": [1, 2],
     *   "lessons": [
     *     {
     *       "title": "Introduction to Laravel",
     *       "type": "video",
     *       "content": "Introduction about Laravel framework",
     *       "video_url": "https://youtube.com/...",
     *       "order_index": 1,
     *       "is_preview": true
     *     },
     *     {
     *       "title": "Setting Up Laravel Project",
     *       "type": "text",
     *       "content": "Step by step guide to setup...",
     *       "order_index": 2,
     *       "is_preview": false
     *     }
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if user is instructor
        if ($user->role !== 'instructor') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya instruktur yang dapat membuat kursus',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:courses',
            'slug' => 'required|string|max:255|unique:courses',
            'description' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'thumbnail_url' => 'nullable|url',
            'quota' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,archived',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'lessons' => 'nullable|array|min:1',
            'lessons.*.title' => 'required|string|max:255',
            'lessons.*.type' => 'required|in:video,text,quiz,assignment',
            'lessons.*.content' => 'required|string',
            'lessons.*.video_url' => 'nullable|url',
            'lessons.*.file_url' => 'nullable|url',
            'lessons.*.order_index' => 'nullable|integer|min:1',
            'lessons.*.is_preview' => 'nullable|boolean',
        ], [
            'title.required' => 'Judul kursus harus diisi',
            'title.unique' => 'Judul kursus sudah ada',
            'slug.required' => 'Slug harus diisi',
            'slug.unique' => 'Slug sudah ada',
            'description.required' => 'Deskripsi harus diisi',
            'level.required' => 'Level harus diisi',
            'level.in' => 'Level harus beginner, intermediate, atau advanced',
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'quota.required' => 'Kuota harus diisi',
            'quota.min' => 'Kuota minimal 1',
            'lessons.min' => 'Minimal satu lesson harus disediakan',
            'lessons.*.title.required' => 'Judul lesson harus diisi',
            'lessons.*.type.required' => 'Tipe lesson harus diisi',
            'lessons.*.type.in' => 'Tipe lesson harus video, text, quiz, atau assignment',
            'lessons.*.content.required' => 'Konten lesson harus diisi',
        ]);

        DB::beginTransaction();
        try {
            // Create course
            $course = Course::create([
                'instructor_id' => $user->id,
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'level' => $validated['level'],
                'price' => $validated['price'],
                'thumbnail_url' => $validated['thumbnail_url'] ?? null,
                'quota' => $validated['quota'],
                'status' => $validated['status'],
                'rating_avg' => 0,
                'rating_count' => 0,
            ]);

            // Attach categories if provided
            if (!empty($validated['category_ids'])) {
                $course->categories()->attach($validated['category_ids']);
            }

            // Create lessons for the course
            $lessons = [];
            if (!empty($validated['lessons'])) {
                foreach ($validated['lessons'] as $index => $lessonData) {
                    $lesson = Lesson::create([
                        'course_id' => $course->id,
                        'title' => $lessonData['title'],
                        'type' => $lessonData['type'],
                        'content' => $lessonData['content'],
                        'video_url' => $lessonData['video_url'] ?? null,
                        'file_url' => $lessonData['file_url'] ?? null,
                        'order_index' => $lessonData['order_index'] ?? ($index + 1),
                        'is_preview' => $lessonData['is_preview'] ?? false,
                    ]);
                    $lessons[] = $lesson;
                }
            }

            DB::commit();

            $course->load(['instructor:id,name,email', 'categories:id,name', 'lessons']);

            return response()->json([
                'success' => true,
                'message' => 'Kursus dan lessons berhasil dibuat',
                'data' => [
                    'course' => $course,
                    'lessons_count' => count($lessons),
                    'lessons' => $lessons,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kursus dan lessons',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update course with lessons management (Only Owner/Instructor)
     * PUT /api/v1/courses/{id}
     * 
     * Request body example:
     * {
     *   "title": "Updated Course Title",
     *   "description": "Updated description",
     *   "price": 349000,
     *   "status": "published",
     *   "category_ids": [1, 2],
     *   "lessons": [
     *     {
     *       "id": 1,
     *       "title": "Updated Lesson Title",
     *       "type": "video",
     *       "content": "Updated content",
     *       "video_url": "https://youtube.com/...",
     *       "order_index": 1,
     *       "is_preview": true,
     *       "action": "update"
     *     },
     *     {
     *       "title": "New Lesson",
     *       "type": "text",
     *       "content": "New lesson content",
     *       "order_index": 2,
     *       "is_preview": false,
     *       "action": "create"
     *     },
     *     {
     *       "id": 3,
     *       "action": "delete"
     *     }
     *   ]
     * }
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        try {
            $course = Course::findOrFail($id);

            // Check authorization - only instructor who owns this course can update
            if ($course->instructor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah kursus ini',
                ], 403);
            }

            $validated = $request->validate([
                'title' => 'nullable|string|max:255|unique:courses,title,' . $id,
                'slug' => 'nullable|string|max:255|unique:courses,slug,' . $id,
                'description' => 'nullable|string',
                'level' => 'nullable|in:beginner,intermediate,advanced',
                'price' => 'nullable|numeric|min:0',
                'thumbnail_url' => 'nullable|url',
                'quota' => 'nullable|integer|min:1',
                'status' => 'nullable|in:draft,published,archived',
                'category_ids' => 'nullable|array',
                'category_ids.*' => 'integer|exists:categories,id',
                'lessons' => 'nullable|array',
                'lessons.*.id' => 'nullable|integer|exists:lessons,id',
                'lessons.*.title' => 'nullable|string|max:255',
                'lessons.*.type' => 'nullable|in:video,text,quiz,assignment',
                'lessons.*.content' => 'nullable|string',
                'lessons.*.video_url' => 'nullable|url',
                'lessons.*.file_url' => 'nullable|url',
                'lessons.*.order_index' => 'nullable|integer|min:1',
                'lessons.*.is_preview' => 'nullable|boolean',
                'lessons.*.action' => 'nullable|in:create,update,delete',
            ]);

            DB::beginTransaction();
            try {
                // Update course data
                $courseData = collect($validated)->except(['lessons', 'category_ids'])->toArray();
                if (!empty($courseData)) {
                    $course->update($courseData);
                }

                // Update categories if provided
                if (isset($validated['category_ids'])) {
                    $course->categories()->sync($validated['category_ids']);
                }

                // Handle lessons management
                if (!empty($validated['lessons'])) {
                    foreach ($validated['lessons'] as $lessonData) {
                        $action = $lessonData['action'] ?? 'update';

                        if ($action === 'create') {
                            // Create new lesson
                            Lesson::create([
                                'course_id' => $course->id,
                                'title' => $lessonData['title'],
                                'type' => $lessonData['type'],
                                'content' => $lessonData['content'],
                                'video_url' => $lessonData['video_url'] ?? null,
                                'file_url' => $lessonData['file_url'] ?? null,
                                'order_index' => $lessonData['order_index'] ?? null,
                                'is_preview' => $lessonData['is_preview'] ?? false,
                            ]);
                        } elseif ($action === 'update' && isset($lessonData['id'])) {
                            // Update existing lesson
                            $lesson = Lesson::findOrFail($lessonData['id']);

                            // Verify lesson belongs to this course
                            if ($lesson->course_id !== $course->id) {
                                throw new \Exception('Lesson tidak termasuk dalam kursus ini');
                            }

                            $updateData = collect($lessonData)
                                ->except(['id', 'action'])
                                ->filter(fn($value) => $value !== null)
                                ->toArray();

                            if (!empty($updateData)) {
                                $lesson->update($updateData);
                            }
                        } elseif ($action === 'delete' && isset($lessonData['id'])) {
                            // Soft delete lesson
                            $lesson = Lesson::findOrFail($lessonData['id']);

                            // Verify lesson belongs to this course
                            if ($lesson->course_id !== $course->id) {
                                throw new \Exception('Lesson tidak termasuk dalam kursus ini');
                            }

                            $lesson->delete();
                        }
                    }
                }

                DB::commit();

                $course->load(['instructor:id,name,email', 'categories:id,name', 'lessons']);

                return response()->json([
                    'success' => true,
                    'message' => 'Kursus dan lessons berhasil diperbarui',
                    'data' => $course,
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kursus',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete course (Only Owner/Instructor) - Soft Delete
     * DELETE /api/v1/courses/{id}
     */
    public function destroy($id)
    {
        $user = Auth::user();

        try {
            $course = Course::findOrFail($id);

            // Check authorization - only instructor who owns this course can delete
            if ($course->instructor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus kursus ini',
                ], 403);
            }

            // Check if course has active enrollments
            if ($course->enrollments()->where('status', 'active')->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kursus yang memiliki enrollment aktif',
                ], 409);
            }

            $courseTitle = $course->title;
            $courseId = $course->id;

            // Perform soft delete
            $course->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kursus berhasil dihapus',
                'data' => [
                    'deleted_course_id' => $courseId,
                    'deleted_course_title' => $courseTitle,
                    'deleted_at' => now(),
                    'note' => 'Kursus dapat dipulihkan kembali jika diperlukan',
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kursus',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore soft deleted course (Only Owner/Instructor)
     * POST /api/v1/courses/{id}/restore
     */
    public function restore($id)
    {
        $user = Auth::user();

        try {
            // Use withTrashed() to find soft-deleted courses
            $course = Course::withTrashed()->findOrFail($id);

            // Check authorization - only instructor who owns this course can restore
            if ($course->instructor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk memulihkan kursus ini',
                ], 403);
            }

            // Check if course is not already deleted
            if (!$course->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kursus tidak dalam status terhapus',
                ], 400);
            }

            // Restore soft deleted course
            $course->restore();
            $course->load(['instructor:id,name,email', 'categories:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Kursus berhasil dipulihkan',
                'data' => $course,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan kursus',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Permanently delete a soft-deleted course (Only Owner/Instructor)
     * DELETE /api/v1/courses/{id}/force
     */
    public function forceDelete($id)
    {
        $user = Auth::user();

        try {
            // Use withTrashed() to find soft-deleted courses
            $course = Course::withTrashed()->findOrFail($id);

            // Check authorization
            if ($course->instructor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus permanen kursus ini',
                ], 403);
            }

            $courseTitle = $course->title;

            // Permanently delete
            $course->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Kursus berhasil dihapus secara permanen',
                'data' => [
                    'permanently_deleted_course' => $courseTitle,
                    'deleted_at' => now(),
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus permanen kursus',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore soft-deleted lesson (Only Instructor who owns the course)
     * POST /api/v1/courses/{courseId}/lessons/{lessonId}/restore
     */
    public function restoreLesson($courseId, $lessonId)
    {
        $user = Auth::user();

        try {
            $course = Course::findOrFail($courseId);

            // Check authorization
            if ($course->instructor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengelola lesson di kursus ini',
                ], 403);
            }

            // Find soft-deleted lesson
            $lesson = Lesson::withTrashed()
                ->where('course_id', $courseId)
                ->findOrFail($lessonId);

            // Check if lesson is actually deleted
            if (!$lesson->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lesson tidak dalam status terhapus',
                ], 400);
            }

            // Restore lesson
            $lesson->restore();

            return response()->json([
                'success' => true,
                'message' => 'Lesson berhasil dipulihkan',
                'data' => $lesson,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus atau Lesson tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan lesson',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Permanently delete a soft-deleted lesson (Only Instructor who owns the course)
     * DELETE /api/v1/courses/{courseId}/lessons/{lessonId}/force
     */
    public function forceDeleteLesson($courseId, $lessonId)
    {
        $user = Auth::user();

        try {
            $course = Course::findOrFail($courseId);

            // Check authorization
            if ($course->instructor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengelola lesson di kursus ini',
                ], 403);
            }

            // Find soft-deleted lesson
            $lesson = Lesson::withTrashed()
                ->where('course_id', $courseId)
                ->findOrFail($lessonId);

            $lessonTitle = $lesson->title;

            // Permanently delete lesson
            $lesson->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Lesson berhasil dihapus secara permanen',
                'data' => [
                    'permanently_deleted_lesson' => $lessonTitle,
                    'deleted_at' => now(),
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus atau Lesson tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus permanen lesson',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
