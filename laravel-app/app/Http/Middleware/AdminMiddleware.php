<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User) {
            // Проверка для PHPStan
            if ($user->isAdmin()) {
                return $next($request);
            }
        }

        return redirect('/catalog')->with('error', 'У вас нет прав доступа к этой странице.');
    }
}
