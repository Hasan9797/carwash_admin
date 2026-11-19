<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Car;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 3 ta Region
        $yunusobod = Region::create([
            'name_uz' => 'Yunusobod filiali',
            'name_ru' => 'Юнусабадский филиал',
            'name_crl' => 'Юнусобод филиали',
            'is_active' => true,
        ]);

        $chilonzor = Region::create([
            'name_uz' => 'Chilonzor filiali',
            'name_ru' => 'Чиланзарский филиал',
            'name_crl' => 'Чилонзор филиали',
            'is_active' => true,
        ]);

        $mirzoUlugbek = Region::create([
            'name_uz' => "Mirzo Ulug'bek filiali",
            'name_ru' => 'Мирзо-Улугбекский филиал',
            'name_crl' => 'Мирзо Улуғбек филиали',
            'is_active' => true,
        ]);

        $regions = collect([$yunusobod, $chilonzor, $mirzoUlugbek]);

        // 2. 4 ta Category
        $cat1 = Category::create([
            'name_uz' => 'Standart yuvish (kuzov)',
            'name_ru' => 'Стандартная мойка (кузов)',
            'name_crl' => 'Стандарт ювиш (кузов)',
            'price' => 45000.00,
            'is_active' => true,
        ]);

        $cat2 = Category::create([
            'name_uz' => 'Kuzov + Salon',
            'name_ru' => 'Кузов + Салон',
            'name_crl' => 'Кузов + Салон',
            'price' => 75000.00,
            'is_active' => true,
        ]);

        $cat3 = Category::create([
            'name_uz' => 'Kompleks VIP yuvish',
            'name_ru' => 'Комплексная VIP мойка',
            'name_crl' => 'Комплекс VIP ювиш',
            'price' => 130000.00,
            'is_active' => true,
        ]);

        $cat4 = Category::create([
            'name_uz' => 'Deteyling va Kimtozalash',
            'name_ru' => 'Детейлинг и химчистка',
            'name_crl' => 'Детейлинг ва кимтозалаш',
            'price' => 280000.00,
            'is_active' => true,
        ]);

        $categories = collect([$cat1, $cat2, $cat3, $cat4]);

        // 3. Foydalanuvchilar (1 admin, 2 operator, 3 washer)
        $admin = User::create([
            'name' => 'Bosh Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'phone' => '+998901234567',
            'region_id' => $yunusobod->id,
            'is_active' => true,
        ]);

        $operator1 = User::create([
            'name' => 'Jasur Operator',
            'email' => 'operator1@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::OPERATOR,
            'phone' => '+998901112233',
            'region_id' => $yunusobod->id,
            'is_active' => true,
        ]);

        $operator2 = User::create([
            'name' => 'Farrux Operator',
            'email' => 'operator2@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::OPERATOR,
            'phone' => '+998904445566',
            'region_id' => $chilonzor->id,
            'is_active' => true,
        ]);

        $operators = collect([$operator1, $operator2]);

        $washer1 = User::create([
            'name' => 'Rustam Qodirov',
            'email' => 'washer1@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::WASHER,
            'phone' => '+998935550101',
            'region_id' => $yunusobod->id,
            'is_active' => true,
        ]);

        $washer2 = User::create([
            'name' => 'Alisher Karimov',
            'email' => 'washer2@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::WASHER,
            'phone' => '+998935550202',
            'region_id' => $chilonzor->id,
            'is_active' => true,
        ]);

        $washer3 = User::create([
            'name' => 'Sanjar Saidov',
            'email' => 'washer3@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::WASHER,
            'phone' => '+998935550303',
            'region_id' => $mirzoUlugbek->id,
            'is_active' => true,
        ]);

        $washers = collect([$washer1, $washer2, $washer3]);

        // 4. Kameradan o'tgan bir nechta mashinalar
        $demoPlates = [
            '01A777AA', '01B123BA', '10C456CA', '30D789DA', '01X999XX',
            '01M001MM', '10T100TT', '01Z555ZZ', '40K321KK', '01H888HH',
        ];

        $cars = collect();
        foreach ($demoPlates as $index => $plate) {
            $cars->push(Car::create([
                'plate_number' => $plate,
                'camera_id' => 'CAM-IN-'.(($index % 3) + 1),
                'image_path' => null,
                'detected_at' => Carbon::now()->subMinutes($index * 15),
                'raw_payload' => [
                    'plate_number' => $plate,
                    'camera_id' => 'CAM-IN-'.(($index % 3) + 1),
                    'timestamp' => Carbon::now()->subMinutes($index * 15)->toIso8601String(),
                ],
            ]));
        }

        // 5. 20 ta Demo Order
        $orderStatuses = [
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::DONE,
            OrderStatus::IN_PROGRESS,
            OrderStatus::IN_PROGRESS,
            OrderStatus::IN_PROGRESS,
            OrderStatus::NEW,
            OrderStatus::NEW,
            OrderStatus::NEW,
            OrderStatus::CANCELLED,
            OrderStatus::CANCELLED,
        ];

        for ($i = 0; $i < 20; $i++) {
            $status = $orderStatuses[$i];
            $category = $categories->random();
            $operator = $operators->random();
            $washer = $washers->random();
            $region = $operator->region ?? $regions->random();

            // Bir nechtasi kameradagi mashina bilan bog'lanadi
            $car = $i < 6 ? $cars[$i] : null;
            $plateNumber = $car ? $car->plate_number : '01'.sprintf('%03d', rand(100, 999)).chr(rand(65, 90)).chr(rand(65, 90));

            // Sanani oxirgi 7 kun ichida taqsimlash, kamida 4-5 tasi bugungi kunga to'g'ri kelsin
            $daysAgo = $i < 6 ? 0 : ($i % 6);
            $createdAt = Carbon::today()->subDays($daysAgo)->setHour(rand(8, 20))->setMinute(rand(0, 59));

            $startedAt = null;
            $finishedAt = null;

            if ($status === OrderStatus::IN_PROGRESS || $status === OrderStatus::DONE) {
                $startedAt = (clone $createdAt)->addMinutes(rand(5, 15));
            }

            if ($status === OrderStatus::DONE) {
                $finishedAt = (clone $startedAt)->addMinutes(rand(25, 50));
            }

            Order::create([
                'car_id' => $car?->id,
                'plate_number' => $plateNumber,
                'category_id' => $category->id,
                'operator_id' => $operator->id,
                'washer_id' => $washer->id,
                'region_id' => $region->id,
                'price' => $category->price,
                'status' => $status,
                'started_at' => $startedAt,
                'finished_at' => $finishedAt,
                'created_at' => $createdAt,
                'updated_at' => $finishedAt ?? $startedAt ?? $createdAt,
            ]);
        }
    }
}
