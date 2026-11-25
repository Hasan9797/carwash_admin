<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\DTO\ReportFilterDTO;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TelegramBotHandler
{
    public function __construct(
        protected TelegramService $telegram,
        protected ReportService $reportService,
        protected OrderService $orderService,
        protected OrderRepositoryInterface $orderRepository,
    ) {}

    public function handleUpdate(array $update): void
    {
        try {
            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);

                return;
            }

            if (isset($update['message'])) {
                $this->handleMessage($update['message']);

                return;
            }
        } catch (\Throwable $e) {
            Log::error('Telegram update processing error: ' . $e->getMessage(), [
                'exception' => $e,
                'update' => $update,
            ]);
        }
    }

    protected function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'] ?? null;
        if (! $chatId) {
            return;
        }

        $text = trim((string) ($message['text'] ?? ''));
        $user = User::query()->where('chat_id', (string) $chatId)->first();

        if (! $user) {
            $this->sendUnregisteredReply($chatId);

            return;
        }

        if (! $user->is_active) {
            $this->telegram->sendMessage(
                $chatId,
                "⚠️ Sizning hisobingiz tizimda faolsizlantirilgan. Iltimos, administrator bilan bog'laning."
            );

            return;
        }

        if ($text === '/start') {
            $this->sendStartGreeting($user, $chatId);

            return;
        }

        if ($text === '/help' || $text === 'ℹ️ Yordam') {
            $this->sendHelp($user, $chatId);

            return;
        }

        if ($user->isAdmin()) {
            $this->handleAdminCommand($user, $chatId, $text);

            return;
        }

        if ($user->isWasher()) {
            $this->handleWasherCommand($user, $chatId, $text);

            return;
        }

        if ($user->isOperator()) {
            $this->telegram->sendMessage(
                $chatId,
                "Assalomu alaykum, <b>{$user->name}</b>!\nSiz tizimda Operator sifatida ro'yxatdan o'tgansiz.\nBuyurtmalarni ochish va boshqarish uchun veb paneldan foydalaning."
            );
        }
    }

    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackId = $callbackQuery['id'];
        $fromId = $callbackQuery['from']['id'] ?? null;
        $data = (string) ($callbackQuery['data'] ?? '');
        $message = $callbackQuery['message'] ?? null;
        $chatId = $message['chat']['id'] ?? $fromId;

        $user = User::query()->where('chat_id', (string) $fromId)->first();
        if (! $user || ! $user->is_active) {
            $this->telegram->answerCallbackQuery($callbackId, "Siz tizimda ro'yxatdan o'tmagansiz!", true);

            return;
        }

        if (str_starts_with($data, 'finish_order_')) {
            $orderId = (int) substr($data, 13);
            $order = $this->orderRepository->findById($orderId);

            if (! $order) {
                $this->telegram->answerCallbackQuery($callbackId, "Zakas topilmadi!", true);

                return;
            }

            // Washer tekshiruvi: faqat o'ziga biriktirilgan zakasni yakunlashi mumkin (admin istalganini)
            if ($user->isWasher() && $order->washer_id !== $user->id) {
                $this->telegram->answerCallbackQuery($callbackId, "Ushbu zakas sizga biriktirilmagan!", true);

                return;
            }

            if ($order->status === OrderStatus::DONE) {
                $this->telegram->answerCallbackQuery($callbackId, "Ushbu zakas allaqachon yakunlangan!");

                return;
            }

            if ($order->status === OrderStatus::CANCELLED) {
                $this->telegram->answerCallbackQuery($callbackId, "Ushbu zakas bekor qilingan!", true);

                return;
            }

            // Agar hali NEW bo'lsa, avval IN_PROGRESS ga, keyin DONE ga o'tkazish
            if ($order->status === OrderStatus::NEW) {
                $this->orderService->updateStatus($order->id, OrderStatus::IN_PROGRESS);
            }

            $success = $this->orderService->updateStatus($order->id, OrderStatus::DONE);

            if ($success) {
                $this->telegram->answerCallbackQuery($callbackId, "Zakas muvaffaqiyatli yakunlandi! ✅");

                $price = number_format((float) $order->price, 0, '.', ' ');
                $this->telegram->sendMessage(
                    $chatId,
                    "✅ <b>Zakas #{$order->id} ({$order->plate_number}) yakunlandi!</b>\n"
                    . "💰 Summa: <b>{$price} so'm</b>\n"
                    . "⏰ Yakunlangan vaqt: " . now()->format('H:i')
                );
            } else {
                $this->telegram->answerCallbackQuery($callbackId, "Zakas holatini o'zgartirib bo'lmadi!", true);
            }
        }
    }

    protected function sendUnregisteredReply(string|int $chatId): void
    {
        $text = "Assalomu alaykum! Siz Carwash tizimida ro'yxatdan o'tmagansiz.\n\n"
            . "Sizning Telegram Chat ID: <code>{$chatId}</code>\n\n"
            . "Iltimos, ushbu ID raqamni tizim administratoriga taqdim eting.";

        $this->telegram->sendMessage($chatId, $text);
    }

    protected function sendStartGreeting(User $user, string|int $chatId): void
    {
        if ($user->isAdmin()) {
            $keyboard = [
                'keyboard' => [
                    [['text' => '📅 Bugungi hisobot'], ['text' => '📆 Haftalik hisobot']],
                    [['text' => '🗓 Oylik hisobot'], ['text' => 'ℹ️ Yordam']],
                ],
                'resize_keyboard' => true,
            ];

            $text = "Assalomu alaykum, <b>{$user->name}</b>!\n"
                . "Carwash boshqaruv panelining Telegram botiga xush kelibsiz.\n"
                . "Siz tizimda <b>Bosh Administrator</b> sifatida ro'yxatdansiz.\n\n"
                . "Hisobotlarni olish uchun quyidagi tugmalardan birini bosing:";

            $this->telegram->sendMessage($chatId, $text, $keyboard);

            return;
        }

        if ($user->isWasher()) {
            $keyboard = [
                'keyboard' => [
                    [['text' => '🚗 Mening jarayondagi zakaslarim']],
                    [['text' => 'ℹ️ Yordam']],
                ],
                'resize_keyboard' => true,
            ];

            $text = "Assalomu alaykum, <b>{$user->name}</b>!\n"
                . "Siz tizimda <b>Yuvuvchi</b> sifatida ro'yxatdansiz.\n\n"
                . "Sizga yangi zakaz biriktirilganda bu yerga xabar yuboriladi.\n"
                . "Jarayondagi zakaslaringizni ko'rish va yakunlash uchun quyidagi tugmadan foydalaning:";

            $this->telegram->sendMessage($chatId, $text, $keyboard);

            return;
        }

        if ($user->isOperator()) {
            $text = "Assalomu alaykum, <b>{$user->name}</b>!\n"
                . "Siz tizimda <b>Operator</b> sifatida ro'yxatdansiz.\n\n"
                . "Buyurtmalarni qabul qilish va boshqarish uchun veb paneldan foydalaning.";

            $this->telegram->sendMessage($chatId, $text);
        }
    }

    protected function sendHelp(User $user, string|int $chatId): void
    {
        if ($user->isAdmin()) {
            $text = "📋 <b>Administrator buyruqlari:</b>\n\n"
                . "• <b>Bugungi hisobot</b> — Bugungi kunda bajarilgan zakazlar va tushum\n"
                . "• <b>Haftalik hisobot</b> — Oxirgi 7 kunlik ko'rsatkichlar\n"
                . "• <b>Oylik hisobot</b> — Joriy oydagi ko'rsatkichlar\n"
                . "• /start — Bosh menyuni qayta chiqarish";
        } elseif ($user->isWasher()) {
            $text = "📋 <b>Yuvuvchi buyruqlari:</b>\n\n"
                . "• <b>Mening jarayondagi zakaslarim</b> — Hozir sizga biriktirilgan va tugallanmagan zakaslar ro'yxati\n"
                . "• Yangi zakaz biriktirilganda darhol xabarnoma keladi va «Zakasni yakunlash» tugmasi chiqadi\n"
                . "• /start — Bosh menyuni qayta chiqarish";
        } else {
            $text = "Carwash boshqaruv tizimi boti. Foydalanish uchun veb-panelga kiring.";
        }

        $this->telegram->sendMessage($chatId, $text);
    }

    protected function handleAdminCommand(User $user, string|int $chatId, string $text): void
    {
        if ($text === '📅 Bugungi hisobot' || $text === '/today') {
            $msg = $this->buildReportMessage(
                'Bugungi',
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay()
            );
            $this->telegram->sendMessage($chatId, $msg);

            return;
        }

        if ($text === '📆 Haftalik hisobot' || $text === '/weekly') {
            $msg = $this->buildReportMessage(
                'Oxirgi 7 kunlik',
                Carbon::today()->subDays(6)->startOfDay(),
                Carbon::today()->endOfDay()
            );
            $this->telegram->sendMessage($chatId, $msg);

            return;
        }

        if ($text === '🗓 Oylik hisobot' || $text === '/monthly') {
            $msg = $this->buildReportMessage(
                'Joriy oylik',
                Carbon::now()->startOfMonth()->startOfDay(),
                Carbon::today()->endOfDay()
            );
            $this->telegram->sendMessage($chatId, $msg);

            return;
        }

        $this->telegram->sendMessage(
            $chatId,
            "Tushunarsiz buyruq. Iltimos, pastdagi menyu tugmalaridan birini tanlang yoki /help buyrug'ini yuboring."
        );
    }

    protected function handleWasherCommand(User $user, string|int $chatId, string $text): void
    {
        if ($text === '🚗 Mening jarayondagi zakaslarim' || $text === '/myorders') {
            $orders = Order::query()
                ->with(['category', 'region'])
                ->where('washer_id', $user->id)
                ->whereIn('status', [OrderStatus::NEW->value, OrderStatus::IN_PROGRESS->value])
                ->orderBy('id', 'desc')
                ->get();

            if ($orders->isEmpty()) {
                $this->telegram->sendMessage(
                    $chatId,
                    "🎉 Hozirda sizda jarayondagi zakaslar yo'q.\nYangi zakaz biriktirilganda sizga xabar beriladi."
                );

                return;
            }

            $this->telegram->sendMessage($chatId, "📋 <b>Sizning faol zakaslaringiz (" . $orders->count() . " ta):</b>");

            foreach ($orders as $ord) {
                $price = number_format((float) $ord->price, 0, '.', ' ');
                $categoryName = $ord->category?->name ?? '—';
                $regionName = $ord->region?->name ?? '—';
                $statusLabel = $ord->status->label();

                $ordText = "🚗 <b>Zakas #{$ord->id}</b>\n"
                    . "Raqam: <code>{$ord->plate_number}</code>\n"
                    . "Xizmat: <b>{$categoryName}</b>\n"
                    . "Narx: <b>{$price} so'm</b>\n"
                    . "Filial: {$regionName}\n"
                    . "Holat: <i>{$statusLabel}</i>";

                $keyboard = [
                    'inline_keyboard' => [
                        [
                            [
                                'text' => "✅ Yakunlash (#{$ord->id})",
                                'callback_data' => "finish_order_{$ord->id}",
                            ],
                        ],
                    ],
                ];

                $this->telegram->sendMessage($chatId, $ordText, $keyboard);
            }

            return;
        }

        $this->telegram->sendMessage(
            $chatId,
            "Tushunarsiz buyruq. Jarayondagi zakaslarni ko'rish uchun pastdagi «Mening jarayondagi zakaslarim» tugmasini bosing."
        );
    }

    public function buildReportMessage(string $title, Carbon $from, Carbon $to): string
    {
        $filter = new ReportFilterDTO(
            dateFrom: $from->toDateString(),
            dateTo: $to->toDateString()
        );

        $categories = $this->reportService->getByCategory($filter);
        $washers = $this->reportService->getByWasher($filter);
        $regions = $this->reportService->getByRegion($filter);

        $totalOrders = (int) $categories->sum('orders_count');
        $totalAmount = (float) $categories->sum('total_amount');
        $averageCheck = $totalOrders > 0 ? ($totalAmount / $totalOrders) : 0;

        $msg = "📊 <b>{$title} hisobot</b>\n"
            . "📅 <i>" . $from->format('d.m.Y') . " — " . $to->format('d.m.Y') . "</i>\n\n"
            . "✅ Bajarilgan zakazlar: <b>{$totalOrders} ta</b>\n"
            . "💰 Jami tushum: <b>" . number_format($totalAmount, 0, '.', ' ') . " so'm</b>\n"
            . "💳 O'rtacha chek: <b>" . number_format($averageCheck, 0, '.', ' ') . " so'm</b>\n\n";

        if ($categories->isNotEmpty()) {
            $msg .= "🧼 <b>Xizmatlar bo'yicha:</b>\n";
            foreach ($categories->take(5) as $cat) {
                $amount = number_format((float) $cat->total_amount, 0, '.', ' ');
                $msg .= "• {$cat->title}: <b>{$cat->orders_count} ta</b> ({$amount} so'm)\n";
            }
            $msg .= "\n";
        }

        if ($washers->isNotEmpty()) {
            $msg .= "👨‍🔧 <b>Yuvuvchilar bo'yicha:</b>\n";
            foreach ($washers->take(5) as $w) {
                $amount = number_format((float) $w->total_amount, 0, '.', ' ');
                $msg .= "• {$w->title}: <b>{$w->orders_count} ta</b> ({$amount} so'm)\n";
            }
            $msg .= "\n";
        }

        if ($regions->count() > 1) {
            $msg .= "🏢 <b>Filiallar bo'yicha:</b>\n";
            foreach ($regions as $r) {
                $amount = number_format((float) $r->total_amount, 0, '.', ' ');
                $msg .= "• {$r->title}: <b>{$r->orders_count} ta</b> ({$amount} so'm)\n";
            }
        }

        return trim($msg);
    }
}
