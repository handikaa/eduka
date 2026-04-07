<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistance\Eloquent\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    /**
     * Get all categories
     * GET /api/categories
     */
    public function index(Request $request)
    {
        try {
            $query = Category::query();

            // Search by name or slug
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            }

            // Sort
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $categories = $query->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'message' => 'Daftar kategori berhasil diambil',
                'data' => $categories->items(),
                'pagination' => [
                    'total' => $categories->total(),
                    'per_page' => $categories->perPage(),
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category detail with list of courses
     * GET /api/categories/{id}
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail kategori berhasil diambil',
                'data' => [
                    'category' => $category,
                    'courses_count' => $category->courses()->count(),
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new category
     * POST /api/categories
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'required|string|max:255|unique:categories',
        ], [
            'name.required' => 'Nama kategori harus diisi',
            'name.unique' => 'Nama kategori sudah ada',
            'slug.required' => 'Slug harus diisi',
            'slug.unique' => 'Slug sudah ada',
            'slug.max' => 'Slug maksimal 255 karakter',
        ]);

        try {
            $category = Category::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dibuat',
                'data' => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update category
     * PUT /api/categories/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'nullable|string|max:255|unique:categories,name,' . $id,
                'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            ], [
                'name.unique' => 'Nama kategori sudah ada',
                'slug.unique' => 'Slug sudah ada',
            ]);

            // Update only if fields are provided
            $updateData = collect($validated)
                ->filter(fn($value) => $value !== null)
                ->toArray();

            if (!empty($updateData)) {
                $category->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui',
                'data' => $category,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete category (if no courses associated)
     * DELETE /api/categories/{id}
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            // Check if category has associated courses
            if ($category->courses()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kategori yang memiliki kursus terkait',
                    'data' => [
                        'courses_count' => $category->courses()->count(),
                    ],
                ], 409);
            }

            $categoryName = $category->name;
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus',
                'data' => [
                    'deleted_category' => $categoryName,
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
