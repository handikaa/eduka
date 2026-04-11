<?php

namespace App\Http\Controllers\Api;

use App\Aplication\Auth\DTOs\ProfileUserDto;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Controllers\Controller;
use App\Aplication\Auth\DTOs\GetUserByIdDto;
use App\Aplication\Auth\DTOs\LoginDto;
use App\Aplication\Auth\DTOs\LogoutUserDto;
use App\Aplication\Auth\DTOs\RegisterDto;
use App\Aplication\Auth\UseCases\GetUserByIdUsecase;
use App\Aplication\Auth\UseCases\LoginUseCase;
use App\Aplication\Auth\UseCases\ProfileUsecase;
use App\Aplication\Auth\UseCases\LogoutUsecase;
use App\Aplication\Auth\UseCases\RegisterUsecase;
use App\Domain\Auth\Exceptions\UserNotFoundException;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Domain\Auth\Exceptions\InactiveUserException;
use App\Domain\Auth\Exceptions\UserAlreadyExistsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use Throwable;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     * POST /api/auth/register
     */
    public function register(
        RegisterRequest $request,
        RegisterUseCase $registerUseCase
    ) {
        try {
            $result = $registerUseCase->execute(
                new RegisterDto(
                    name: $request->string('name')->toString(),
                    email: $request->string('email')->toString(),
                    password: $request->string('password')->toString(),
                    role: $request->string('role')->toString(),
                )
            );

            return ApiResponse::success(
                data: [
                    'user' => [
                        'id' => $result['user']->id,
                        'name' => $result['user']->name,
                        'email' => $result['user']->email,
                        'role' => $result['user']->role,
                    ],
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                ],
                message: 'Registrasi berhasil',
                code: 201,
            );
        } catch (UserAlreadyExistsException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 409,
            );
        } catch (\Throwable $e) {
            return ApiResponse::error(
                message: 'Registrasi gagal',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Handle user login.
     * POST /api/auth/login
     */
    public function login(LoginRequest $request, LoginUseCase $loginUseCase)
    {
        try {
            $result = $loginUseCase->execute(
                new LoginDto(
                    email: $request->string('email')->toString(),
                    password: $request->string('password')->toString(),
                )
            );

            return ApiResponse::success(
                data: [
                    'user' => [
                        'id' => $result['user']->id,
                        'name' => $result['user']->name,
                        'email' => $result['user']->email,
                        'role' => $result['user']->role,
                    ],
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                ],
                message: 'Login berhasil',
                code: 200,
            );
        } catch (InvalidCredentialsException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 401,
            );
        } catch (InactiveUserException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403,
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Login gagal',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Get user by ID.
     * GET /api/auth/user/{id}
     */
    public function getUserById(GetUserByIdUsecase $usecase, int $id)
    {
        try {
            $result = $usecase->execute(new GetUserByIdDto(id: $id));
            return ApiResponse::success(
                data: [
                    'user' => [
                        'id' => $result['user']->id,
                        'name' => $result['user']->name,
                        'email' => $result['user']->email,
                        'role' => $result['user']->role,
                    ],
                ],
                message: 'User ditemukan',
                code: 200,
            );
        } catch (UserNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404,
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'User tidak ditemukan',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Handle user logout.
     * POST /api/auth/logout
     */
    public function logout(LogoutUsecase $logoutUsecase,   Request $request,)
    {
        try {
            $logoutUsecase->execute(
                new LogoutUserDto(
                    user: $request->user(),
                )
            );

            return ApiResponse::success(
                message: 'Logout berhasil',
                code: 200,
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'User tidak ditemukan',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Get current user profile.
     * GET /api/auth/profile
     */
    public function me(Request $request, ProfileUsecase $usecase)
    {
        try {
            $result = $usecase->execute(
                new ProfileUserDto(
                    user: $request->user(),
                )
            );
            return ApiResponse::success(
                data: [
                    'user' => [
                        'id' => $result['user']->id,
                        'name' => $result['user']->name,
                        'email' => $result['user']->email,
                        'role' => $result['user']->role,
                    ],
                ],
                message: 'Profile user ditemukan',
                code: 200,
            );
        } catch (UserNotFoundException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 404,
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'User tidak ditemukan',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }
}
