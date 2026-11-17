<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyCameraWebhookToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = (string) config('services.camera.webhook_token', '');
        $headerToken = (string) $request->header('X-Webhook-Token', '');

        if ($configuredToken === '' || ! hash_equals($configuredToken, $headerToken)) {
            Log::channel('camera')->warning('Unauthorized camera webhook attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'received_token_length' => strlen($headerToken),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Noto\'g\'ri yoki mavjud bo\'lmagan webhook token (Unauthorized).',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
