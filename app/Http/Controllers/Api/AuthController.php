<?php

namespace App\Http\Controllers\Api;

use App\Aplication\Auth\DTOs\LoginDto;
use App\Aplication\Auth\DTOs\RegisterDto;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Http\Responses\ApiResponse;
use App\Aplication\Auth\UseCases\LoginUseCase;
use App\Aplication\Auth\UseCases\RegisterUsecase;
use App\Http\Controllers\Controller;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Domain\Auth\Exceptions\InactiveUserException;
use App\Domain\Auth\Exceptions\UserAlreadyExistsException;
use Throwable;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     * POST /api/auth/register
     */
    public function register(RegisterRequest $request, RegisterUseCase $registerUseCase)
    {
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
     * Handle user logout.
     * POST /api/auth/logout
     */
    // public function logout(Request $request)
    // {
    //     try {
    //         $request->user()->currentAccessToken()->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Logout berhasil',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Logout gagal',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    /**
     * Get current user profile.
     * GET /api/auth/profile
     */
    // public function profile(Request $request)
    // {
    //     $user = $request->user();

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'id' => $user->id,
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'role' => $user->role,
    //             'is_active' => $user->is_active,
    //             'avatar_url' => $user->avatar_url,
    //             'created_at' => $user->created_at,
    //         ],
    //     ], 200);
    // }

    // /**
    //  * Get user by ID.
    //  * GET /api/auth/user/{id}
    //  */
    // public function getUserById($id)
    // {
    //     try {
    //         $user = User::findOrFail($id);

    //         return response()->json([
    //             'success' => true,
    //             'data' => [
    //                 'id' => $user->id,
    //                 'name' => $user->name,
    //                 'email' => $user->email,
    //                 'role' => $user->role,
    //                 'is_active' => $user->is_active,
    //                 'avatar_url' => $user->avatar_url,
    //                 'created_at' => $user->created_at,
    //                 'updated_at' => $user->updated_at,
    //             ],
    //         ], 200);
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'User tidak ditemukan',
    //         ], 404);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal mengambil data user',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
