<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\CarWebhookDTO;
use App\Jobs\ProcessCameraImageJob;
use App\Models\Car;
use App\Repositories\Contracts\CarRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class CarService
{
    public function __construct(
        protected CarRepositoryInterface $carRepository,
    ) {}

    /**
     * @return array{car: Car, created: bool}
     */
    public function registerFromCamera(CarWebhookDTO $dto): array
    {
        $normalizedPlate = Car::normalizePlateNumber($dto->plateNumber);

        Log::channel('camera')->info('Camera webhook car registration request', [
            'plate_number' => $normalizedPlate,
            'camera_id' => $dto->cameraId,
            'detected_at' => $dto->detectedAt?->toIso8601String(),
        ]);

        // Idempotency: Oxirgi 2 daqiqa ichida shu raqam kelsa, qayta yaratmaymiz
        $existing = $this->carRepository->findRecentByPlate($normalizedPlate, 2);
        if ($existing) {
            Log::channel('camera')->info('Duplicate plate detected within 2 minutes. Idempotent skip.', [
                'car_id' => $existing->id,
                'plate_number' => $normalizedPlate,
            ]);

            return [
                'car' => $existing,
                'created' => false,
            ];
        }

        $car = $this->carRepository->create([
            'plate_number' => $normalizedPlate,
            'camera_id' => $dto->cameraId,
            'image_path' => null,
            'detected_at' => $dto->detectedAt ?? Carbon::now(),
            'raw_payload' => $dto->rawPayload,
        ]);

        if (! empty($dto->image)) {
            ProcessCameraImageJob::dispatch($car->id, $dto->image);
        }

        Log::channel('camera')->info('New car registered from camera successfully', [
            'car_id' => $car->id,
            'plate_number' => $car->plate_number,
        ]);

        return [
            'car' => $car,
            'created' => true,
        ];
    }

    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->carRepository->paginate($search, $perPage);
    }

    public function getById(int $id): ?Car
    {
        return $this->carRepository->findById($id);
    }

    public function getRecentUnordered(int $limit = 15): Collection
    {
        return $this->carRepository->getRecentUnordered($limit);
    }
}
