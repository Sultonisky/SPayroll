<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Content-Security-Policy', $this->buildCsp());

        return $response;
    }

    /**
     * Build the CSP string dynamically, especially for Vite during development.
     */
    protected function buildCsp(): string
    {
        $viteUrl = $this->getViteUrl();

        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com https://cdn.datatables.net https://app.midtrans.com",
            "style-src 'self' 'unsafe-inline' data: https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net",
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' blob: data: https://api.midtrans.com https://app.midtrans.com https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com https://cdn.datatables.net http://127.0.0.1:5173 ws://127.0.0.1:5173",
            "frame-src 'self' blob: https://www.youtube.com https://www.youtube-nocookie.com https://maps.google.com https://*.google.com https://player.vimeo.com",
            "worker-src 'self' blob:",
            "form-action 'self'",
            "base-uri 'self'",
        ];

        if ($viteUrl) {
            $wsUrl = str_replace(['http://', 'https://'], ['ws://', 'wss://'], $viteUrl);

            // Add Vite Dev Server to relevant sources
            $csp[1] .= " {$viteUrl}"; // script-src
            $csp[2] .= " {$viteUrl}"; // style-src
            $csp[5] .= " {$viteUrl} {$wsUrl}"; // connect-src
        }

        return implode('; ', $csp).';';
    }

    /**
     * Get the current Vite development server URL if it's running.
     */
    protected function getViteUrl(): ?string
    {
        $hotFile = public_path('hot');

        if (file_exists($hotFile)) {
            $url = trim(file_get_contents($hotFile));

            return rtrim($url, '/');
        }

        return null;
    }
}
