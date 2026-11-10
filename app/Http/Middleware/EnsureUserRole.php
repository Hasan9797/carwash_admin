<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! $user->is_active) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Sizning hisobingiz faol emas.']);
        }

        $userRoleValue = $user->role->value;
        $userRoleSlug = $user->role->nameSlug();

        $allowed = false;
        foreach ($roles as $role) {
            $trimmed = strtolower(trim($role));
            if (is_numeric($trimmed) && (int) $trimmed === $userRoleValue) {
                $allowed = true;
                break;
            }
            if ($trimmed === $userRoleSlug) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            abort(Response::HTTP_FORBIDDEN, "Sizda ushbu amalni bajarish uchun yetarli huquq yo'q.");
        }

        return $next($request);
    }
}
