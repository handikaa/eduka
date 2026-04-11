<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Aplication\Category\DTOs\CreateCategoryDto;
use App\Aplication\Category\DTOs\DeleteCategoryDto;
use App\Aplication\Category\DTOs\GetCategoryByIdDto;
use App\Aplication\Category\DTOs\UpdateCategoryDto;
use App\Domain\Category\Exceptions\CategoryAlreadyExistsException;
use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Aplication\Category\UseCases\CreateCategoryUsecase;
use App\Aplication\Category\UseCases\DeleteCategoryUsecase;
use App\Aplication\Category\UseCases\GetAllCategoryUsecase;
use App\Aplication\Category\UseCases\GetCategoryByIdUsecase;
use App\Aplication\Category\UseCases\UpdateCategoryUsecase;

use Throwable;

class CategoryController extends Controller
{
    /**
     * Get all categories
     * GET /api/categories
     */
    public function index(GetAllCategoryUsecase $useCase)
    {
        try {
            $result = $useCase->execute();

            return ApiResponse::success(
                data: $result['categories'],
                message: 'List category berhasil diambil',
                code: 200
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil data category',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }


    /**
     * Get category detail with list of courses
     * GET /api/categories/{id}
     */
    public function show(GetCategoryByIdUsecase $useCase, int $id)
    {
        try {
            $result = $useCase->execute(
                new GetCategoryByIdDto(id: $id)
            );

            return ApiResponse::success(
                data: [
                    'category' => [
                        'id' => $result['category']->id,
                        'name' => $result['category']->name,
                        'slug' => $result['category']->slug,
                    ]
                ],
                message: 'Category ditemukan',
                code: 200
            );
        } catch (CategoryNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil category',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }
    /**
     * Create new category
     * POST /api/categories
     */
    public function store(
        StoreCategoryRequest $request,
        CreateCategoryUsecase $useCase
    ) {
        try {
            $result = $useCase->execute(
                new CreateCategoryDto(
                    name: $request->string('name')->toString(),
                    slug: $request->string('slug')->toString(),
                )
            );

            return ApiResponse::success(
                data: [
                    'category' => [
                        'id' => $result['category']->id,
                        'name' => $result['category']->name,
                        'slug' => $result['category']->slug,
                    ]
                ],
                message: 'Category berhasil dibuat',
                code: 201
            );
        } catch (CategoryAlreadyExistsException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 409
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal membuat category',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Update category
     * PUT /api/categories/{id}
     */

    public function update(
        UpdateCategoryRequest $request,
        UpdateCategoryUsecase $useCase,
        int $id
    ) {
        try {
            $result = $useCase->execute(
                new UpdateCategoryDto(
                    id: $id,
                    name: $request->string('name')->toString(),
                )
            );

            return ApiResponse::success(
                data: [
                    'category' => [
                        'id' => $result['category']->id,
                        'name' => $result['category']->name,
                        'slug' => $result['category']->slug,
                    ]
                ],
                message: 'Category berhasil diupdate',
                code: 200
            );
        } catch (CategoryNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (CategoryAlreadyExistsException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 409
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal update category',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Delete category (if no courses associated)
     * DELETE /api/categories/{id}
     */
    public function destroy(DeleteCategoryUsecase $useCase, int $id)
    {

        try {
            $useCase->execute(new DeleteCategoryDto(id: $id));
            return ApiResponse::success(
                message: 'Category berhasil dihapus',
                code: 200
            );
        } catch (CategoryNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal menghapus category',
                code: 500,
                errors: [
                    'exception' => $e->getMessage()
                ]
            );
        }
    }
}
