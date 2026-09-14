<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    /**
     * Handle incoming request and check against active legacy redirects.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only process GET/HEAD requests
        if (! $request->isMethodSafe()) {
            return $next($request);
        }

        $rawPath = trim($request->getPathInfo(), '/');

        // Ignore homepage and admin/system paths
        if (empty($rawPath) || str_starts_with($rawPath, 'admin') || str_starts_with($rawPath, 'livewire') || str_starts_with($rawPath, 'build')) {
            return $next($request);
        }

        $candidates = [
            $rawPath,
            '/'.$rawPath,
            '/'.$rawPath.'/',
            $rawPath.'/',
        ];

        $redirect = Redirect::where('is_active', true)
            ->whereIn('source_path', $candidates)
            ->first();

        if ($redirect) {
            $redirect->incrementQuietly('hit_count');
            $redirect->forceFill(['last_accessed_at' => now()])->saveQuietly();

            $destination = $redirect->destination_path;
            $statusCode = in_array((int) $redirect->status_code, [301, 302, 307, 308], true)
                ? (int) $redirect->status_code
                : 301;

            return redirect($destination, $statusCode);
        }

        return $next($request);
    }
}
