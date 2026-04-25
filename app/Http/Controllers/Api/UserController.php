<?php

namespace App\Http\Controllers\Api;

use Throwable;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Aplication\Users\DTOs\GetUsersDto;
use App\Aplication\Users\UseCases\GetUsersUsecase;
use App\Domain\Users\Exceptions\UnauthorizedToViewUsersException;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(
        Request $request,
        GetUsersUsecase $usecase
    ) {
        try {
            $authUser = $request->user();

            if (! $authUser) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetUsersDto(
                perPage: (int) $request->query('per_page', 10),
                page: (int) $request->query('page', 1),
                role: $request->query('role'),
                search: $request->query('search'),
            );

            $result = $usecase->execute(
                authUser: $authUser,
                dto: $dto
            );

            return ApiResponse::successPaginated(
                data: $result->items(),
                pagination: [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ],
                message: 'Berhasil mengambil daftar user'
            );
        } catch (UnauthorizedToViewUsersException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil daftar user',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }
}