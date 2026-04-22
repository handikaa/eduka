<?php

namespace App\Http\Controllers\Api;

use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Aplication\Dashboard\UseCases\GetStudentDashboardSummaryUsecase;
use App\Aplication\Dashboard\UseCases\GetInstructorDashboardSummaryUsecase;
use App\Aplication\Dashboard\UseCases\GetStudentContinueLearningUsecase;
use App\Aplication\Dashboard\UseCases\GetInstructorCoursePerformanceUsecase;
use App\Aplication\Dashboard\DTOs\GetStudentDashboardSummaryDto;
use App\Aplication\Dashboard\DTOs\GetInstructorDashboardSummaryDto;
use App\Aplication\Dashboard\DTOs\GetStudentContinueLearningDto;
use App\Aplication\Dashboard\DTOs\GetInstructorCoursePerformanceDto;
use App\Domain\Dashboard\Exceptions\OnlyStudentCanAccessDashboardException;
use App\Domain\Dashboard\Exceptions\OnlyInstructorCanAccessDashboardException;

class DashboardController extends Controller
{
    public function studentSummary(
        Request $request,
        GetStudentDashboardSummaryUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetStudentDashboardSummaryDto();

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: $result,
                message: 'Berhasil mengambil ringkasan dashboard student'
            );
        } catch (OnlyStudentCanAccessDashboardException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil ringkasan dashboard student',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }
    public function instructorSummary(
        Request $request,
        GetInstructorDashboardSummaryUsecase $usecase
    ) {
        try {
            $instructor = $request->user();

            if (! $instructor) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetInstructorDashboardSummaryDto();

            $result = $usecase->execute(
                instructor: $instructor,
                dto: $dto
            );

            return ApiResponse::success(
                data: $result,
                message: 'Berhasil mengambil ringkasan dashboard instructor'
            );
        } catch (OnlyInstructorCanAccessDashboardException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil ringkasan dashboard instructor',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }
    public function studentContinueLearning(
        Request $request,
        GetStudentContinueLearningUsecase $usecase
    ) {
        try {
            $student = $request->user();

            if (! $student) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetStudentContinueLearningDto();

            $result = $usecase->execute(
                student: $student,
                dto: $dto
            );

            return ApiResponse::success(
                data: [
                    'continue_learning' => $result
                ],
                message: 'Berhasil mengambil data continue learning student'
            );
        } catch (OnlyStudentCanAccessDashboardException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil data continue learning student',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    public function instructorCoursePerformance(
        Request $request,
        GetInstructorCoursePerformanceUsecase $usecase
    ) {
        try {
            $instructor = $request->user();

            if (! $instructor) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    code: 401
                );
            }

            $dto = new GetInstructorCoursePerformanceDto();

            $result = $usecase->execute(
                instructor: $instructor,
                dto: $dto
            );

            return ApiResponse::success(
                data: [
                    'courses' => $result
                ],
                message: 'Berhasil mengambil performa course instructor'
            );
        } catch (OnlyInstructorCanAccessDashboardException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 403
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Gagal mengambil performa course instructor',
                code: 500,
                errors: [
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }
}
