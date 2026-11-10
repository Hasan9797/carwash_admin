<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CameraWebhookRequest;
use App\Services\CameraPayloadMapper;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CameraWebhookController extends Controller
{
    public function __construct(
        protected CameraPayloadMapper $mapper,
        protected CarService $carService,
    ) {}

    public function handle(CameraWebhookRequest $request): JsonResponse
    {
        try {
            $dto = $this->mapper->map($request->all());
            $result = $this->carService->registerFromCamera($dto);

            $statusCode = $result['created'] ? Response::HTTP_CREATED : Response::HTTP_OK;

            return response()->json([
                'status' => 'success',
                'message' => $result['created']
                    ? 'Avtomobil muvaffaqiyatli qabul qilindi va ro\'yxatga olindi.'
                    : 'Avtomobil allaqachon so\'nggi daqiqalarda qayd etilgan (idempotent skip).',
                'data' => [
                    'id' => $result['car']->id,
                    'plate_number' => $result['car']->plate_number,
                    'detected_at' => $result['car']->detected_at?->toIso8601String(),
                    'is_new' => $result['created'],
                ],
            ], $statusCode);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Serverda xatolik yuz berdi: '.$e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
