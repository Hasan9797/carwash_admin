<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTO\CarWebhookDTO;
use App\Services\CarService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SimulateCameraWebhook extends Command
{
    protected $signature = 'camera:simulate 
                            {plate? : Avtomobil davlat raqami (masalan: 01A777AA)}
                            {--camera=CAM-01 : Kamera identifikatori}
                            {--image= : Avtomobil rasmi URL manzili}';

    protected $description = 'Kamera webhook orqali avtomobil kelishini simulyatsiya qilish';

    public function handle(CarService $carService): int
    {
        $plate = $this->argument('plate');
        if (! $plate) {
            $regions = ['01', '10', '30', '40', '60', '80'];
            $letters = chr(rand(65, 90)).chr(rand(65, 90)).chr(rand(65, 90));
            $numbers = sprintf('%03d', rand(100, 999));
            $plate = $regions[array_rand($regions)].$numbers.$letters;
        }

        $cameraId = (string) $this->option('camera');
        $image = (string) ($this->option('image') ?? 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600');
        $now = Carbon::now();

        $this->info('Kamera simulyatsiyasi boshlandi...');
        $this->line("Raqam: <comment>{$plate}</comment>");
        $this->line("Kamera: <comment>{$cameraId}</comment>");
        $this->line("Vaqt: <comment>{$now->format('Y-m-d H:i:s')}</comment>");

        $dto = new CarWebhookDTO(
            plateNumber: $plate,
            cameraId: $cameraId,
            image: $image,
            detectedAt: $now,
            rawPayload: [
                'plate_number' => $plate,
                'camera_id' => $cameraId,
                'image' => $image,
                'simulated' => true,
                'timestamp' => $now->toIso8601String(),
            ],
        );

        $result = $carService->registerFromCamera($dto);

        if ($result['created']) {
            $this->info("✓ YANGI avtomobil bazaga muvaffaqiyatli yozildi (ID: {$result['car']->id})");
            $this->line("Operator endi ushbu mashinaga 'Yangi zakaz ochish' sahifasida buyurtma ochishi mumkin.");
        } else {
            $this->warn("! Dublikat aniqlandi: bu mashina oxirgi 2 daqiqa ichida kelgan edi (Idempotent skip). ID: {$result['car']->id}");
        }

        return self::SUCCESS;
    }
}
