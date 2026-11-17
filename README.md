# Carwash boshqaruv paneli (Avtomoyka CRM)

Senior Laravel darajasida noldan ishlab chiqilgan, zamonaviy va kengaytiriluvchi **Carwash boshqaruv paneli**. Loyiha avtomatik kamera webhook integratsiyasi, operator ish stoli, buyurtmalar holatini real vaqtda kuzatish, avtomatlashtirilgan agregat hisobotlar va tizim ma'muriyatini o'z ichiga oladi.

---

## 🛠 Texnologik stack

- **PHP:** 8.5+ (`declare(strict_types=1);`, PHP 8 Enums, Constructor property promotion, Type-hinting)
- **Framework:** Laravel 12 (Framework v13)
- **Ma'lumotlar bazasi:** MySQL (Testlar uchun SQLite In-Memory)
- **Kesh va Navbatlar (Queue/Cache/Session):** Redis (`predis/predis`, Cache: DB 1, Queue: DB 2, Session: DB 3)
- **Frontend (Monolit):** Blade + Tailwind CSS (v4 Vite) + Alpine.js + Chart.js
- **Eksport:** `maatwebsite/excel` (Excel xlsx eksport)
- **Sifat va Testlar:** Pest PHP (`tests: 17 passed, 102 assertions`), Laravel Pint (PSR-12)
- **Enum & Indexing:** `UserRole` va `OrderStatus` DB'da `tinyInteger` (`int`), ko'p tilli label'lar (`uz`, `en`, `ru`)
- **Debug:** `barryvdh/laravel-debugbar`

---

## 🏛 Arxitektura: Controller → Service → Repository → Model

Loyiha qat'iy qatlamli arxitekturaga asoslangan:

```
[ HTTP Request ]
       │
       ▼
[ FormRequest ] (Validatsiya va DTO tayyorlash)
       │
       ▼
[ Controller ] (Faqat Service'ni chaqiradi, view yoki redirect qaytaradi)
       │
       ▼
[ Service ] (Butun biznes logika shu yerda, DTO qabul qiladi, cache boshqaradi)
       │
       ▼
[ Repository Interface ] ──> [ Eloquent Repository ] (Faqat DB so'rovlar, agregatsiya, pagination)
                                      │
                                      ▼
                                  [ Model ]
```

- **Controller**: Hech qanday to'g'ridan-to'g'ri Eloquent so'rovlari yoki biznes logika yo'q. Faqat `FormRequest` orqali validatsiyadan o'tgan ma'lumotlarni Service'ga uzatadi.
- **Service**: Biznes qoidalar, narx snapshotlari, idempotency tekshiruvi, Redis cache tozalash va Job dispatch qilish shu qatlamda.
- **Repository**: Har bir model uchun interfeys (`app/Repositories/Contracts`) va Eloquent implementatsiyasi (`app/Repositories/Eloquent`) yaratilgan hamda `RepositoryServiceProvider` orqali bog'langan (IoC Container).
- **Agregatsiyalar**: Hisobotlardagi `COUNT`, `SUM`, `AVG` hisoblari Collection darajasida emas, to'g'ridan-to'g'ri MySQL `GROUP BY` darajasida bajariladi.

---

## 🚀 O'rnatish va ishga tushirish

### 1. Talablar
- PHP 8.3 yoki PHP 8.5 (yoqilgan kengaytmalar: `pdo_mysql`, `gd`, `zip`, `fileinfo`, `curl`)
- Composer 2.x
- Node.js 18+ va NPM
- MySQL 8.x
- Redis server (port: 6379)

### 2. Loyihani yuklash va sozlash

```bash
# Repozitoriyaga kiring
cd c:\Users\user\Documents\projects\carwash

# Bog'liqliklarni o'rnatish (agar o'rnatilmagan bo'lsa)
composer install
npm install

# Muhit faylini yaratish
cp .env.example .env

# Ilova kalitini yaratish
php artisan key:generate
```

### 3. `.env` sozlamalari

`.env` faylida quyidagi konfiguratsiyalarni tekshiring:

```env
APP_NAME="Carwash boshqaruv paneli"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Asia/Tashkent
APP_LOCALE=uz

# Ma'lumotlar bazasi
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=carwash
DB_USERNAME=root
DB_PASSWORD=

# Redis konfiguratsiyasi (alohida DB'lar bilan)
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_QUEUE_DB=2
REDIS_SESSION_DB=3

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_CONNECTION=session

# Kamera Webhook xavfsizlik kaliti
CAMERA_WEBHOOK_TOKEN=camera_webhook_secret_key_12345
```

### 4. Migratsiya va Baza ma'lumotlarini to'ldirish (Seeder)

```bash
php artisan migrate --seed
```

Seeder quyidagi demo ma'lumotlarni yaratadi:
- **Admin**: `admin@example.com` / `password` (Bosh administrator)
- **Operatorlar**:
  - `operator1@example.com` / `password` (Yunusobod filiali)
  - `operator2@example.com` / `password` (Chilonzor filiali)
- **Yuvuvchilar (Washers)**: 3 nafar xodim (Rustam, Alisher, Sanjar)
- **Filiallar (Regions)**: Yunusobod, Chilonzor, Mirzo Ulug'bek
- **Xizmat turlari (Categories)**: Standart, Kuzov+Salon, Kompleks VIP, Deteyling
- **20 ta demo zakazlar** va kameradan kelgan mashinalar

### 5. Frontendni build qilish

```bash
npm run build
# yoki rivojlantirish jarayonida:
npm run dev
```

### 6. Queue Worker (Navbat ishchisi)ni ishga tushirish

Kameradan kelgan rasmlarni orqa fonda yuklash va saqlash Redis navbati orqali ishlaydi:

```bash
php artisan queue:work redis --queue=default --tries=3
```

### 7. Mahalliy serverni ishga tushirish

```bash
php artisan serve
```
Brauzerda oching: `http://localhost:8000`

### 8. Production muhitiga chiqarish (Deployment checklist)

Production serverda maksimal tezlik va xavfsizlikni ta'minlash bo'yicha qadamlar:

```bash
# 1. Bog'liqliklarni optimallashtirib o'rnatish
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 2. Fayllar saqlash havzasi havolasini yaratish
php artisan storage:link

# 3. Bazani migratsiya qilish
php artisan migrate --force

# 4. Keshlarni optimallashtirish
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Navbat ishchisi (Supervisor yoki Systemd orqali orqa fonda)
php artisan queue:work redis --queue=default --tries=3 --timeout=60
```

---

## 📸 Kamera Webhook API (Hujjat va cURL namunalari)

Kamera tizimlari avtomobil kirganda quyidagi endpointga so'rov yuboradi.

- **Endpoint:** `POST /api/webhooks/camera`
- **Xavfsizlik:** `X-Webhook-Token` headeri majburiy (xavfsiz `hash_equals` bilan solishtiriladi).
- **Idempotency:** Bir xil davlat raqami 2 daqiqa ichida qayta yuborilsa, tizim dublikat yaratmaydi (`200 OK`, `is_new: false`).
- **Logging:** Barcha so'rovlar `storage/logs/camera.log` kanaliga yoziladi.

### cURL orqali sinov:

#### 1. Yangi avtomobilni qabul qilish (Muvaffaqiyatli - 201 Created):
```bash
curl -X POST http://localhost:8000/api/webhooks/camera \
  -H "Content-Type: application/json" \
  -H "X-Webhook-Token: camera_webhook_secret_key_12345" \
  -d '{
    "plate_number": "01A777AA",
    "camera_id": "CAM-IN-01",
    "image": "https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600",
    "detected_at": "2026-11-10T11:00:00Z"
  }'
```

**Javob (201 Created):**
```json
{
  "status": "success",
  "message": "Avtomobil muvaffaqiyatli qabul qilindi va ro'yxatga olindi.",
  "data": {
    "id": 11,
    "plate_number": "01A777AA",
    "detected_at": "2026-11-10T11:00:00+00:00",
    "is_new": true
  }
}
```

#### 2. Qayta yuborilganda (Idempotent skip - 200 OK):
```json
{
  "status": "success",
  "message": "Avtomobil allaqachon so'nggi daqiqalarda qayd etilgan (idempotent skip).",
  "data": {
    "id": 11,
    "plate_number": "01A777AA",
    "detected_at": "2026-11-10T11:00:00+00:00",
    "is_new": false
  }
}
```

#### 3. Token noto'g'ri bo'lsa (401 Unauthorized):
```bash
curl -X POST http://localhost:8000/api/webhooks/camera \
  -H "Content-Type: application/json" \
  -H "X-Webhook-Token: notogri-token" \
  -d '{"plate_number": "01A777AA"}'
```

---

## 🎨 UI Tuzilmasi (Frontend komponentlari bo'yicha qo'llanma)

Loyihada **Blade + Tailwind CSS + Alpine.js** ishlatilgan. Vue yoki React kabi og'ir SPA frameworklar yo'q, barcha sahifalar toza monolit holda komponentlarga ajratilgan.

### 1. Mavjud Blade komponentlari:
- `<x-card title="..." :action="...">`: Oq fonli, sarlavhali zamonaviy konteyner karta.
- `<x-stat-card title="..." :value="..." color="blue|emerald|amber|indigo">`: Dashboard va hisobotlar yuqori qismidagi ko'rsatkichlar kartasi.
- `<x-table :empty="..." emptyText="...">`: Gorizontal chiziqli, sarlavha va bo'sh holatni (`Ma'lumot topilmadi`) chiroyli ko'rsatuvchi jadval.
- `<x-input label="..." name="..." type="..." value="...">`: Avtomatik xatolik ko'rsatuvchi form input.
- `<x-select label="..." name="...">`: Standartlashtirilgan forma select maydoni.
- `<x-button variant="primary|secondary|success|danger|outline" size="sm|md|lg">`: Tugma komponenti.
- `<x-badge color="blue|emerald|amber|rose|slate">`: Rangli status belgilari (Yangi, Jarayonda, Bajarildi va h.k.).
- `<x-alert type="success|danger|warning|info">`: Flash xabarnomalar uchun chiroyli xabarlar bloki.

### 2. Backendchi yangi sahifa qo'shishi uchun oddiy qo'llanma:
Yangi sahifa qo'shish uchun:
1. `routes/web.php` da marshrutni e'lon qiling.
2. Tegishli Controller metodida view qaytaring (`return view('modul.nomi', [...])`).
3. View faylida quyidagi shablon asosida yozing:

```blade
<x-app-layout>
    <x-slot name="header">
        Sahifa sarlavhasi
    </x-slot>

    <!-- 1. Yuqori sarlavha va harakat tugmasi -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Mening sahifam</h2>
            <p class="text-xs text-slate-500">Qisqa tavsif</p>
        </div>
        <a href="...">
            <x-button variant="primary">Qo'shish</x-button>
        </a>
    </div>

    <!-- 2. Asosiy karta yoki jadval -->
    <x-card class="p-0">
        <x-table :empty="$items->isEmpty()">
            <x-slot name="head">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Nomi</th>
            </x-slot>

            @foreach($items as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->id }}</td>
                    <td class="px-4 py-3">{{ $item->name }}</td>
                </tr>
            @endforeach
        </x-table>
    </x-card>
</x-app-layout>
```

---

## 🧪 Avtomatlashtirilgan testlar va Code Style

Barcha testlar **Pest PHP** da yozilgan:
- `CameraWebhookTest`: Token tekshiruvi, ma'lumotlar saqlanishi, rasm navbati, 2 daqiqalik idempotency kafolati.
- `OrderServiceTest`: Zakaz ochish, kategoriya narxini snapshot qilish, holat o'zgartirish, Dashboard keshi tozalanishi.
- `DashboardTest`: Ruxsatlar, statistik hisob-kitoblar, Redis keshida saqlanishi.
- `AuthTest`: Admin/Operator login, xato parol, Washer kira olmasligi, Role middleware cheklovi.
- `ReportTest`: Kategoriya bo'yicha DB darajasidagi agregatsiya va Excel eksport.
- `EnumsTest`: Integer enum qiymatlari, ko'p tilli tarjimalar (`uz`, `ru`, `en`), DB'ga `int` saqlanishi va model kastingi.

Testlarni ishga tushirish:
```bash
php artisan test
```

Code style (Laravel Pint):
```bash
./vendor/bin/pint --test
# Avtomatik tuzatish:
./vendor/bin/pint
```
