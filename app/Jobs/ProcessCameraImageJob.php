<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Car;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessCameraImageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public int $backoff = 5;

    public function __construct(
        public int $carId,
        public string $imageData,
    ) {}

    public function handle(): void
    {
        $car = Car::find($this->carId);
        if (! $car) {
            Log::channel('camera')->warning('Car not found for image processing', ['car_id' => $this->carId]);

            return;
        }

        try {
            $fileName = 'cars/'.Str::uuid().'.jpg';
            $binaryData = null;

            if (filter_var($this->imageData, FILTER_VALIDATE_URL)) {
                $response = Http::timeout(10)->get($this->imageData);
                if ($response->successful()) {
                    $binaryData = $response->body();
                }
            } elseif (str_starts_with($this->imageData, 'data:image')) {
                $parts = explode(',', $this->imageData);
                $binaryData = base64_decode(end($parts));
            } else {
                $decoded = base64_decode($this->imageData, true);
                if ($decoded !== false) {
                    $binaryData = $decoded;
                }
            }

            if ($binaryData) {
                Storage::disk('public')->put($fileName, $binaryData);
                $car->update(['image_path' => $fileName]);

                Log::channel('camera')->info('Camera image processed and stored successfully', [
                    'car_id' => $car->id,
                    'file' => $fileName,
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('camera')->error('Error processing camera image', [
                'car_id' => $this->carId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
